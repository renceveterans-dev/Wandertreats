package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatEditText;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.cardview.widget.CardView;

import com.google.android.material.appbar.MaterialToolbar;
import com.wandertech.wandertreats.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.databinding.ActivityVerifyBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.MultiTextWatcher;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;
import java.util.Random;

public class VerifyActivity  extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow;
    private ActivityVerifyBinding binding;

    private AppCompatTextView titleTxt;
    private MaterialToolbar toolbar;
    private AppCompatTextView mobilenumberTxt, resendBtn, noteTxt;
    private CardView itemCard1, itemCard2, itemCard3, itemCard4, itemCard5, itemCard6;
    private AppCompatEditText digit1, digit2, digit3, digit4, digit5, digit6;
    private AppCompatEditText[] digitArr = new AppCompatEditText[6];

    private String verificationCode = "";
    private CountDownTimer countDnTimer;
    private AppCompatButton submitBtn;
    private boolean isProcessRunning = false;
    private String verificationType = "";
    private String email = "";
    private String mobile = "";
    private String firstname = "";
    private String lastname = "";
    private  StringBuffer submittedCode;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityVerifyBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        email =  getIntent().getStringExtra("email");
        mobile =  getIntent().getStringExtra("mobileNumber");
        firstname = getIntent().getStringExtra("firstname");
        lastname = getIntent().getStringExtra("lastname");
        verificationType = appFunctions.getJsonValue("APP_VERIFICATION_TYPE", appFunctions.retrieveValue(Utils.APP_GENERAL_DATA));

        initView();
        setLabel();
        setListener();

    }

    private void setLabel() {

        if(verificationType.equalsIgnoreCase(Utils.EMAIL_VERIFICATOION)){
            noteTxt.setText("We sent the code verification to your email address");
            mobilenumberTxt.setText(appFunctions.getEmail());
        }

        if(verificationType.equalsIgnoreCase(Utils.MOBILE_VERIFICATOION)){
            noteTxt.setText("We sent the code verification to your mobile number");
            mobilenumberTxt.setText(appFunctions.getMobileNumber());
        }
    }

    private void setListener() {

        int pos = 0;

        submittedCode = new StringBuffer("");

        for (AppCompatEditText editText : digitArr){
            int position = pos;
            new MultiTextWatcher()
                .registerEditText(editText)
                .setCallback(new MultiTextWatcher.TextWatcherWithInstance() {
                    @Override
                    public void afterTextChanged(AppCompatEditText editText, Editable editable) {

                        if(editable.length() == 1){

                            submittedCode.append(editable.toString());

                            if(position < digitArr.length-1){
                                digitArr[position].clearFocus();
                                digitArr[position+1].requestFocus();
                                digitArr[position+1].setCursorVisible(true);
                            }
                        }else{

                            submittedCode.deleteCharAt(submittedCode.length()-1);

                            if(position > 0){
                                digitArr[position].clearFocus();
                                digitArr[position-1].requestFocus();
                                digitArr[position-1].setCursorVisible(true);
                            }

                        }

                        if(editText.getId() == R.id.digit6 && editable.length() == 1){
                            if(submittedCode.toString().equalsIgnoreCase(verificationCode)){
                                hideKeyboard();
                                verify(verificationCode, verificationType);
                            }else{
                                appFunctions.showMessage("Invalid Code");
                            }


                        }
                    }
                });

            pos++;
        }

        //appFunctions.showMessage(verificationType);

        if(verificationType.equalsIgnoreCase(Utils.EMAIL_VERIFICATOION)){
            sendEmailVerification();
        }

        if(verificationType.equalsIgnoreCase(Utils.MOBILE_VERIFICATOION)){
            sendSMSVerification();
        }

    }

    private void initView() {

        titleTxt = binding.mainToolbar.titleTxt;
        toolbar = findViewById(R.id.toolbar);

        digit1 = binding.digit1;
        digit2 = binding.digit2;
        digit3 = binding.digit3;
        digit4 = binding.digit4;
        digit5 = binding.digit5;
        digit6 = binding.digit6;

        digitArr[0] = binding.digit1;
        digitArr[1] = binding.digit2;
        digitArr[2] = binding.digit3;
        digitArr[3] = binding.digit4;
        digitArr[4] = binding.digit5;
        digitArr[5] = binding.digit6;


        mobilenumberTxt = binding.mobilenumberTxt;
        noteTxt = binding.noteTxt;
        submitBtn = binding.submitBtn;
        resendBtn = binding.resendBtn;
        resendBtn.setOnClickListener(new setOnClickAct());
        submitBtn.setOnClickListener(new setOnClickAct());

        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });


    }

    public void verify(String code, String verificationType){


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("userId", appFunctions.getMemberId());;
        parameters.put("verificationType", verificationType);
        parameters.put("userType", Utils.app_type);

       // appFunctions.showMessage(parameters.toString());

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_confirm_verification.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                //appFunctions.showMessage(responseString);

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){
                        appFunctions.storeData(Utils.USER_PROFILE_JSON, appFunctions.getJsonValue("data", responseString));
                        finish();
                    }

                }

                //
            }
        });
        exeWebServer.execute();

    }

    public void sendEmailVerification(){

        verificationCode = String.valueOf(new Random().nextInt(899999) + 100000);

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("email", appFunctions.getEmail());;
        parameters.put("firstname", appFunctions.getFirstName());
        parameters.put("code", verificationCode);
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_email_verification.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                //appFunctions.showMessage(responseString);

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){
                        verificationCode = appFunctions.getJsonValue("code", responseString);
                        showTimer("Mobile");
                    }

                }

                //
            }
        });
        exeWebServer.execute();

    }



    public void sendSMSVerification(){

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOGIN");
        parameters.put("mobileNumber", "09760449723");
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_sms_verification.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                //appFunctions.showMessage(responseString);

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){
                        verificationCode = appFunctions.getJsonValue("code", responseString);
                        showTimer("Mobile");
                    }

                }

                //
            }
        });
        exeWebServer.execute();

    }

    private void setTime(long milliseconds, String showTimerFor) {
        int minutes = (int) (milliseconds / 1000) / 60;
        int seconds = (int) (milliseconds / 1000) % 60;

        resendBtn.setText(minutes+":"+seconds);
    }

    public void showTimer(String showTimerFor) {
        countDnTimer = new CountDownTimer(60000, 1000) {
            @Override
            public void onTick(long milliseconds) {
                isProcessRunning = true;
                setTime(milliseconds, showTimerFor);
            }

            @Override
            public void onFinish() {
                isProcessRunning = false;
                // this function will be called when the timecount is finishe
                removecountDownTimer();
            }
        }.start();

    }

    private void removecountDownTimer() {
        resendBtn.setText("Resend Now");
        if (countDnTimer != null) {
            countDnTimer.cancel();
            countDnTimer = null;
            isProcessRunning = false;
        }
    }

    private void hideKeyboard(){

        InputMethodManager inputManager = (InputMethodManager) getActContext().getSystemService(Context.INPUT_METHOD_SERVICE);
        inputManager.hideSoftInputFromWindow(this.getCurrentFocus().getWindowToken(),InputMethodManager.HIDE_NOT_ALWAYS);

    }


    private Context getActContext() {

        return VerifyActivity.this;
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){

                case R.id.resendBtn:

                    if(resendBtn.getText().toString().equalsIgnoreCase("Resend now") && !isProcessRunning){
                        if(verificationType.equalsIgnoreCase(Utils.EMAIL_VERIFICATOION)){
                            sendEmailVerification();
                        }

                        if(verificationType.equalsIgnoreCase(Utils.MOBILE_VERIFICATOION)){
                            sendSMSVerification();
                        }
                    }


                    break;

                case R.id.submitBtn:

                    if(submittedCode.toString().equalsIgnoreCase(verificationCode)){
                        hideKeyboard();
                        verify(verificationCode, verificationType);
                    }else{
                        appFunctions.showMessage("Invalid Code");
                    }

                    break;

                default:
                    break;



            }

        }


    }
}
