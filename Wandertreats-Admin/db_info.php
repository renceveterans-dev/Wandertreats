
<?php

    Class Connection {


        private  $server = "mysql:host=renceveteransdev19047.ipagemysql.com;dbname=wandertreats";

        private  $user = "wandertreats";

        private  $pass = "K-anne050915$";
        
        //  private  $server = "mysql:host=localhost;dbname=trikaroo_bsd";
        
        // private  $user = "hengyen_admin";
        
        // private  $pass = "HengYen66888$";

        private $options  = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,);
        
        protected $con;
 
      	public function openConnection(){

             try{
    
                    $this->con = new PDO($this->server, $this->user,$this->pass,$this->options);
                
                    //echo "Connected";
                    return $this->con;
    
            } catch (PDOException $e){

                 echo "There is some problem in connection: " . $e->getMessage();

            }

       	}
    
        public function closeConnection() {
        
           	$this->con = null;
        
        }
        
    }

?>