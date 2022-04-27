package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;

import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.databinding.ActivityForgotPasswordBinding;
import com.wandertech.wandertreats.databinding.ActivityLoginBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.PopUpDialog;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;

public class ForgotPasswordActivity  extends AppCompatActivity {

    private ActivityForgotPasswordBinding binding;
    private View contentView;
    private GeneralFunctions appFunctions;
    private final Handler handler = new Handler();
    private AppCompatTextView titleTxt, forgotPasswordTxt;
    private TextInputLayout emailTxtLayout, passwordTxtLayout;
    private TextInputEditText emailTxt, passwordTxt;
    private AppCompatButton submitBtn;
    private AppCompatImageView backImgView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);


        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityForgotPasswordBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());


        initView();
        setLabel();


    }

    private void initView() {
        
        
        titleTxt = binding.toolbar.titleTxt;
        emailTxtLayout = binding.emailTxtLayout;
        emailTxt = binding.emailTxt;
        
        submitBtn = binding.submitBtn;
        backImgView = binding.toolbar.backImgView;
        forgotPasswordTxt = binding.forgotPasswordTxt;

       // backImgView.setImageDrawable(getDrawable(R.drawable.close));

        submitBtn.setOnClickListener(new setOnClickAct());
        backImgView.setOnClickListener(new setOnClickAct());
        emailTxt.addTextChangedListener(new setOnTextChangeAct(emailTxt));


    }

    private void setLabel() {

        titleTxt.setText("");
    }

    public class setOnTextChangeAct implements TextWatcher {
        public TextInputEditText editText;
        public setOnTextChangeAct(TextInputEditText editText){
            this.editText = editText;
        }
        @Override
        public void beforeTextChanged(CharSequence s, int start, int count, int after) {
        }
        @Override
        public void onTextChanged(CharSequence s, int start, int before, int count) {

            emailTxtLayout.setHelperText(null);
            emailTxtLayout.setHelperTextEnabled(false);
            emailTxtLayout.setErrorEnabled(false);

        }

        @Override
        public void afterTextChanged(Editable s) {
            switch (editText.getId()) {

                case R.id.emailTxt:

                    if(emailTxt.getText().length() == 0 ){

                        emailTxtLayout.setErrorEnabled(true);
                        emailTxtLayout.setHelperTextEnabled(true);
                        emailTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    break;

            }
        }
    }

    public void checkEmail() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "CHECK_EMAIL");
        parameters.put("email", emailTxt.getText().toString() == null ? "" : emailTxt.getText().toString());
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_request_for_reset_password.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {



                if(responseString != null){

                    try{

                        if(appFunctions.getJsonValue("response", responseString).equalsIgnoreCase("0")){
                            emailTxtLayout.setErrorEnabled(true);
                            emailTxtLayout.setHelperTextEnabled(true);
                            emailTxtLayout.setHelperText("Email is not associted with a registered account.");

                            return;
                        }

                        PopUpDialog dialog = new PopUpDialog(getActContext(), "Reset Password");
                        dialog.setMessage("We have sent a reset password link to you email "+emailTxt.getText().toString());
                        dialog.setPositiveBtn("Okay");
                        dialog.show();
                        dialog.getPositive_btn().setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                dialog.dismiss();
                            }
                        });

                    }catch (Exception e){




                    }

                }

                //
            }
        });
        exeWebServer.execute();
    }

    public class setOnClickAct implements View.OnClickListener {

        private static final long MIN_CLICK_INTERVAL=600;
        private long mLastClickTime;

        @Override
        public void onClick(View view) {

            long currentClickTime= SystemClock.uptimeMillis();
            long elapsedTime=currentClickTime-mLastClickTime;

            mLastClickTime=currentClickTime;

            if(elapsedTime<=MIN_CLICK_INTERVAL)
                return;

            switch(view.getId()){

                case R.id.backImgView:

                    onBackPressed();
                    break;

                case R.id.submitBtn:



                    try{

                        emailTxtLayout.setHelperText(null);
                        emailTxtLayout.setHelperTextEnabled(false);
                        emailTxtLayout.setErrorEnabled(false);


                        checkEmail();
                    }catch (Exception e){
                        Toast.makeText(getActContext(), ""+e.toString(), Toast.LENGTH_SHORT).show();
                    }


                    break;
                case R.id.forgotPasswordTxt:
                    Toast.makeText(getActContext(),"haha ", Toast.LENGTH_SHORT).show();
                    break;



            }

        }


    }



    public Context getActContext() {
        return this;
    }
}

