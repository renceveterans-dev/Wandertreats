package com.wandertech.wandertreats.store;

import android.app.Activity;
import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Patterns;
import android.view.View;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatCheckBox;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;

import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.store.databinding.ActivityRegisterBinding;
import com.wandertech.wandertreats.store.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.PopUpDialog;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.utils.Utils;

import java.util.HashMap;

public class RegisterActivity extends AppCompatActivity {

    private ActivityRegisterBinding binding;
    private View contentView;
    private GeneralFunctions appFunctions;
    private final Handler handler = new Handler();
    private AppCompatTextView titleTxt;
    private AppCompatButton registerBtn, loginBtn;
    private TextInputLayout fNameTxtLayout, lNameTxtLayout, emailTxtLayout, mobileTxtLayout, passwordTxtLayout, rePasswordTxtLayout;
    private TextInputEditText fNameTxt, lNameTxt, emailTxt, mobileTxt, passwordTxt, rePasswordTxt;
    private MaterialToolbar toolbar;
    private AppCompatImageView backImgView;
    private AppCompatImageView closeImgView;
    private AppCompatCheckBox agreeCheckBox;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.white, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.white));
        }

        getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR);//  set status text dark

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityRegisterBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());


        initView();
        setLabel();


    }

    private void initView() {

        contentView = binding.contentView;
        toolbar = binding.mainToolbar.toolbar;
        titleTxt = binding.mainToolbar.titleTxt;
        backImgView = binding.mainToolbar.backImgView;
       // backImgView.setBackgroundResource(R.drawable.close);

        fNameTxt = binding.fNameTxt;
        lNameTxt = binding.lNameTxt;
        emailTxt = binding.emailTxt;
        mobileTxt = binding.mobileTxt;
        passwordTxt = binding.passwordTxt;
        rePasswordTxt = binding.rePasswordTxt;

        fNameTxtLayout = binding.fNameTxtLayout;
        lNameTxtLayout = binding.lNameTxtLayout;
        emailTxtLayout = binding.emailTxtLayout;
        mobileTxtLayout = binding.mobileTxtLayout;
        passwordTxtLayout = binding.passwordTxtLayout;
        rePasswordTxtLayout = binding.rePasswordTxtLayout;

        agreeCheckBox = binding.agreeCheckBox;

        registerBtn = binding.registerBtn;
        loginBtn = binding.loginBtn;

        fNameTxt.addTextChangedListener(new setOnTextChangeAct(fNameTxt));
        lNameTxt.addTextChangedListener(new setOnTextChangeAct(lNameTxt));
        emailTxt.addTextChangedListener(new setOnTextChangeAct(emailTxt));
        mobileTxt.addTextChangedListener(new setOnTextChangeAct(mobileTxt));
        passwordTxt.addTextChangedListener(new setOnTextChangeAct(passwordTxt));
        rePasswordTxt.addTextChangedListener(new setOnTextChangeAct(rePasswordTxt));

        registerBtn.setOnClickListener(new setOnClickAct());
        loginBtn.setOnClickListener(new setOnClickAct());

        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

    }

    private void hideSoftKeyboard() {

        try{
            InputMethodManager imm = (InputMethodManager) getSystemService(Activity.INPUT_METHOD_SERVICE);
            imm.toggleSoftInput(InputMethodManager.HIDE_IMPLICIT_ONLY, 0);
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        }catch (Exception e){
           appFunctions.showMessage(e.toString());
        }
    }

    public static void hideKeyboard(Activity activity) {
        InputMethodManager imm = (InputMethodManager) activity.getSystemService(Activity.INPUT_METHOD_SERVICE);
        //Find the currently focused view, so we can grab the correct window token from it.
        View view = activity.getCurrentFocus();
        //If no view currently has focus, create a new one, just so we can grab a window token from it
        if (view == null) {
            view = new View(activity);
        }
        imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
    }



    private void setLabel() {

        titleTxt.setText("");
    }

    public class setOnTextChangeAct implements TextWatcher{
        public TextInputEditText editText;
        public setOnTextChangeAct(TextInputEditText editText){
            this.editText = editText;
        }
        @Override
        public void beforeTextChanged(CharSequence s, int start, int count, int after) {
        }
        @Override
        public void onTextChanged(CharSequence s, int start, int before, int count) {



            switch (editText.getId()) {



                case R.id.fNameTxt:

                    fNameTxtLayout.setHelperText(null);
                    fNameTxtLayout.setHelperTextEnabled(false);
                    fNameTxtLayout.setErrorEnabled(false);

                    break;

                case R.id.lNameTxt:

                    lNameTxtLayout.setHelperText(null);
                    lNameTxtLayout.setHelperTextEnabled(false);
                    lNameTxtLayout.setErrorEnabled(false);

                    break;

                case R.id.emailTxt:

                    emailTxtLayout.setHelperText(null);
                    emailTxtLayout.setHelperTextEnabled(false);
                    emailTxtLayout.setErrorEnabled(false);

                    break;

                case R.id.mobileTxt:

                    mobileTxtLayout.setHelperText(null);
                    mobileTxtLayout.setHelperTextEnabled(false);
                    mobileTxtLayout.setErrorEnabled(false);



                    break;



                case R.id.passwordTxt:

                    passwordTxtLayout.setHelperText(null);
                    passwordTxtLayout.setHelperTextEnabled(false);
                    passwordTxtLayout.setErrorEnabled(false);



                    break;

                case R.id.rePasswordTxt:

                    rePasswordTxtLayout.setHelperText(null);
                    rePasswordTxtLayout.setHelperTextEnabled(false);
                    rePasswordTxtLayout.setErrorEnabled(false);


                    break;

            }


        }

        @Override
        public void afterTextChanged(Editable s) {
            switch (editText.getId()) {

                case R.id.fNameTxt:

                    if(fNameTxt.getText().length() == 0 ){

                        fNameTxtLayout.setErrorEnabled(true);
                        fNameTxtLayout.setHelperTextEnabled(true);
                        fNameTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    break;

                case R.id.lNameTxt:

                    if(lNameTxt.getText().length() == 0 ){

                        lNameTxtLayout.setErrorEnabled(true);
                        lNameTxtLayout.setHelperTextEnabled(true);
                        lNameTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    break;

                case R.id.emailTxt:

                    if(emailTxt.getText().length() == 0 ){

                        emailTxtLayout.setErrorEnabled(true);
                        emailTxtLayout.setHelperTextEnabled(true);
                        emailTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(emailTxt.getText().length() > 0  && !Patterns.EMAIL_ADDRESS.matcher(emailTxt.getText().toString().trim()).matches() ){
                        if(!emailTxtLayout.isErrorEnabled()){
                            emailTxtLayout.setErrorEnabled(true);
                            emailTxtLayout.setHelperTextEnabled(true);
                            emailTxtLayout.setHelperText("Invalid email.");
                        }

                    }

                    break;

                case R.id.mobileTxt:

                    if(mobileTxt.getText().length() == 0 ){

                        mobileTxtLayout.setErrorEnabled(true);
                        mobileTxtLayout.setHelperTextEnabled(true);
                        mobileTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(mobileTxt.getText().length() != 11 ){

                        mobileTxtLayout.setErrorEnabled(true);
                        mobileTxtLayout.setHelperTextEnabled(true);
                        mobileTxtLayout.setHelperText("Invalid mobile number.");

                        return;
                    }

                    break;



                case R.id.passwordTxt:

                    if(passwordTxt.getText().length() == 0 ){

                        passwordTxtLayout.setErrorEnabled(true);
                        passwordTxtLayout.setHelperTextEnabled(true);
                        passwordTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    break;

                case R.id.rePasswordTxt:

                    if(rePasswordTxt.getText().length() == 0 ){

                        rePasswordTxtLayout.setErrorEnabled(true);
                        rePasswordTxtLayout.setHelperTextEnabled(true);
                        rePasswordTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(rePasswordTxt.getText().length() > 0 && !rePasswordTxt.getText().toString().equalsIgnoreCase(passwordTxt.getText().toString())){

                        rePasswordTxtLayout.setErrorEnabled(true);
                        rePasswordTxtLayout.setHelperTextEnabled(true);
                        rePasswordTxtLayout.setHelperText("Password doest matched.");

                        return;
                    }

                    break;

            }
        }
    }

    public void register() {

        hideKeyboard((Activity) getActContext());


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "REGISTER");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("firstName", fNameTxt.getText().toString() == null ? "" : fNameTxt.getText().toString());;
        parameters.put("lastName", lNameTxt.getText().toString() == null ? "" : lNameTxt.getText().toString() );
        parameters.put("email", emailTxt.getText().toString() == null ? "" : emailTxt.getText().toString() );
        parameters.put("mobileNumber", mobileTxt.getText().toString() == null ? "" : mobileTxt.getText().toString() );
        parameters.put("password", passwordTxt.getText().toString() == null ? "" : passwordTxt.getText().toString() );
        parameters.put("displayPhoto", "");
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_register.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                //Toast.makeText(getActContext(),responseString, Toast.LENGTH_SHORT).show();

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        appFunctions.storeData(Utils.isUserLogIn, "1");
                        appFunctions.storeData(Utils.iMemberId, appFunctions.getJsonValue("iUserId", responseString));
                        appFunctions.storeData(Utils.USER_PROFILE_JSON, appFunctions.getJsonValue("result", responseString));
                        appFunctions.storeData(Utils.USER_FIRST_LOGIN, "1");

                        new StartActProcess(getActContext()).startAct(WelcomeActivity.class);

                    }else{

                        PopUpDialog dialog = new PopUpDialog(getActContext(), "Registration Failed");
                        dialog.setMessage(appFunctions.getJsonValue("error", responseString));
                        dialog.setPositiveBtn("Okay");
                        dialog.show();
                        dialog.getPositive_btn().setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                dialog.dismiss();
                            }
                        });

                        //Toast.makeText(getActContext(), "FAILED : "+responseString, Toast.LENGTH_SHORT).show();
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

                case R.id.closeImgView:
                    onBackPressed();

                    break;

                case R.id.loginBtn:
                    new StartActProcess(getActContext()).startAct(LoginActivity.class);
                    break;

                case R.id.registerBtn:

                    if(fNameTxt.getText().length() == 0 ){

                        fNameTxtLayout.setErrorEnabled(true);
                        fNameTxtLayout.setHelperTextEnabled(true);
                        fNameTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(lNameTxt.getText().length() == 0 ){

                        lNameTxtLayout.setErrorEnabled(true);
                        lNameTxtLayout.setHelperTextEnabled(true);
                        lNameTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(emailTxt.getText().length() == 0 ){

                        emailTxtLayout.setErrorEnabled(true);
                        emailTxtLayout.setHelperTextEnabled(true);
                        emailTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(emailTxt.getText().length() > 0  && !Patterns.EMAIL_ADDRESS.matcher(emailTxt.getText().toString().trim()).matches() ){
                        if(!emailTxtLayout.isErrorEnabled()){
                            emailTxtLayout.setErrorEnabled(true);
                            emailTxtLayout.setHelperTextEnabled(true);
                            emailTxtLayout.setHelperText("Invalid email.");
                        }

                    }

                    if(mobileTxt.getText().length() == 0 ){

                        mobileTxtLayout.setErrorEnabled(true);
                        mobileTxtLayout.setHelperTextEnabled(true);
                        mobileTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(mobileTxt.getText().length() != 11 ){

                        mobileTxtLayout.setErrorEnabled(true);
                        mobileTxtLayout.setHelperTextEnabled(true);
                        mobileTxtLayout.setHelperText("Invalid mobile number.");

                        return;
                    }

                    if(passwordTxt.getText().length() == 0 ){

                        passwordTxtLayout.setErrorEnabled(true);
                        passwordTxtLayout.setHelperTextEnabled(true);
                        passwordTxtLayout.setHelperText("This field is required.");

                        return;
                    }



                    if(rePasswordTxt.getText().length() == 0 ){

                        rePasswordTxtLayout.setErrorEnabled(true);
                        rePasswordTxtLayout.setHelperTextEnabled(true);
                        rePasswordTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(rePasswordTxt.getText().length() > 0 && !rePasswordTxt.getText().toString().equalsIgnoreCase(passwordTxt.getText().toString())){

                        rePasswordTxtLayout.setErrorEnabled(true);
                        rePasswordTxtLayout.setHelperTextEnabled(true);
                        rePasswordTxtLayout.setHelperText("Password doest matched.");

                        return;
                    }

                    if(rePasswordTxt.getText().length() > 0 && !rePasswordTxt.getText().toString().equalsIgnoreCase(passwordTxt.getText().toString())){

                        rePasswordTxtLayout.setErrorEnabled(true);
                        rePasswordTxtLayout.setHelperTextEnabled(true);
                        rePasswordTxtLayout.setHelperText("Password doest matched.");

                        return;
                    }

                    if(!agreeCheckBox.isChecked()){
                        appFunctions.showMessageDialog("Please check the checkbox to confirm that you agree to our Terms and conditions.");
                        return;
                    }

                    register();

//                    if(fNameTxt.getText().length() != 0 && lNameTxt.getText().length() != 0 && emailTxt.getText().length() != 0 && Patterns.EMAIL_ADDRESS.matcher(emailTxt.getText().toString().trim()).matches() && rePasswordTxt.getText().length() != 0 && rePasswordTxt.getText().toString().equalsIgnoreCase(passwordTxt.getText().toString())){
//
//
//                    }

                    break;
                default:
                    Toast.makeText(getActContext(),"haha ", Toast.LENGTH_SHORT).show();
                    break;



            }

        }


    }


    public Context getActContext() {
        return RegisterActivity.this;
    }
}


