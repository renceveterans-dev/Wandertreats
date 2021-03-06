<?php
namespace Ably;

use Ably\Exceptions\AblyException;
use Ably\Models\ChannelOptions;
use Ably\Models\Message;
use Ably\Models\PaginatedResult;

/**
 * Represents a channel
 * @property-read Presence $presence Presence object for this channel
 * @method publish(Message $message)
 * @method publish(string $name, string $data)
 */
class Channel {

    private $name;
    private $channelPath;
    private $ably;
    private $presence;
    /**
     * @var Ably\Models\ChannelOptions
     */
    public $options;

    /**
     * Constructor
     * @param AblyRest $ably Ably API instance
     * @param string $name Channel's name
     * @param ChannelOptions|array|null $options Channel options (for encrypted channels)
     * @throws AblyException
     */
    public function __construct( AblyRest $ably, $name, $options = [] ) {
        $this->ably = $ably;
        $this->name = $name;
        $this->channelPath = "/channels/" . urlencode( $name );
        $this->presence = new Presence( $ably, $this );

        $this->setOptions( $options );
    }

    /**
     * Magic getter for the $presence property
     */
    public function __get( $name ) {
        if ($name == 'presence') {
            return $this->presence;
        }

        throw new AblyException( 'Undefined property: '.__CLASS__.'::'.$name );
    }

    /**
     * Posts a message to this channel
     * @param mixed ... Either a Message, array of Message-s, or (string eventName, string data, [string clientId])
     * @throws \Ably\Exceptions\AblyException
     */
    public function __publish_request_body(...$args) {

        // Process arguments
        $messages = [];
        $argsn = count($args);
        if ( $argsn == 1 && is_a( $args[0], 'Ably\Models\Message' ) ) { // single Message
            $messages[] = $args[0];
        } else if ( $argsn == 1 && is_array( $args[0] ) ) { // array of Messages
            $messages = $args[0];
        } else if ( $argsn >= 2 && $argsn <= 4 ) { // eventName, data[, clientId][, extras]
            $msg = new Message();
            $msg->name = $args[0];
            $msg->data = $args[1];
            if ( $argsn == 3 ) {
                if ( is_string($args[2]) )
                    $msg->clientId = $args[2];
                else if ( is_array($args[2]) )
                    $msg->extras = $args[2];
            } else if ( count($args) == 4 ) {
                $msg->clientId = $args[2];
                $msg->extras = $args[3];
            }

            $messages[] = $msg;
        } else {
            throw new AblyException(
                'Wrong parameters provided, use either Message, array of Messages, or name and data', 40003, 400
            );
        }

        // Cipher and Idempotent
        $emptyId = true;
        foreach ( $messages as $msg ) {
            if ( $this->options->cipher ) {
                $msg->setCipherParams( $this->options->cipher );
            }
            if ( $msg->id ) {
                $emptyId = false;
            }
        }

        if ($emptyId && $this->ably->options->idempotentRestPublishing) {
            $baseId = base64_encode( openssl_random_pseudo_bytes(12) );
            foreach ( $messages as $key => $msg ) {
              $msg->id = $baseId . ":" . $key;
            }
        }

        // Serialize
        $json = '';
        if ( count($messages) == 1) {
            $json = $messages[0]->toJSON();
        } else {
            $jsonArray = [];
            foreach ( $messages as $msg ) {
                $jsonArray[] = $msg->toJSON();
            }
            $json = '[' . implode( ',', $jsonArray ) . ']';
        }

        // if the message has a clientId set and we're using token based auth,
        // the clientIds must match unless we're a wildcard client
        $authClientId = $this->ably->auth->clientId;
        if ( !empty( $msg->clientId ) && !$this->ably->auth->isUsingBasicAuth()
             && $authClientId != '*' && $msg->clientId != $authClientId) {
            throw new AblyException(
                'Message\'s clientId does not match the clientId of the authorisation token.', 40102, 401
            );
        }

        return $json;
    }

    public function publish(...$args) {

        $json = $this->__publish_request_body(...$args);

        $this->ably->post( $this->channelPath . '/messages', $headers = [], $json );
        return true;
    }

    /**
     * Retrieves channel's history of messages
     * @param array $params Parameters to be sent with the request
     * @return PaginatedResult
     */
    public function history( $params = [] ) {
        return new PaginatedResult( $this->ably, 'Ably\Models\Message', $this->getCipherParams(), 'GET', $this->getPath() . '/messages', $params );
    }

    /**
     * @return string Channel's name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string Channel portion of the request URI
     */
    public function getPath() {
        return $this->channelPath;
    }

    /**
     * @return CipherParams|null Cipher params if the channel is encrypted
     */
    public function getCipherParams() {
        return $this->options->cipher;
    }

    /**
     * @return \Ably\Models\ChannelOptions
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Sets channel options
     * @param array|null $options channel options
     * @throws AblyException
     */
    public function setOptions( $options = [] ) {
        $this->options = new ChannelOptions( $options );
    }
}
