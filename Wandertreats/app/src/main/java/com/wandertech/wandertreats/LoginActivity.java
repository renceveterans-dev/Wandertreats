package com.wandertech.wandertreats;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.WindowInsets;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;

import com.facebook.AccessToken;
import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.FacebookSdk;
import com.facebook.GraphRequest;
import com.facebook.appevents.AppEventsLogger;
import com.facebook.login.LoginManager;
import com.facebook.login.LoginResult;
import com.facebook.login.widget.LoginButton;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.databinding.ActivityLauncherBinding;
import com.wandertech.wandertreats.databinding.ActivityLoginBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.PopUpDialog;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;

public class LoginActivity extends AppCompatActivity {

    private ActivityLoginBinding binding;
    private View contentView;
    private GeneralFunctions appFunctions;
    private final Handler handler = new Handler();
    private AppCompatTextView titleTxt, forgotPasswordTxt;
    private TextInputLayout usernameTxtLayout, passwordTxtLayout;
    private TextInputEditText usernameTxt, passwordTxt;
    private AppCompatButton loginBtn;
    private LinearLayoutCompat  loginFbBtn;
    private AppCompatImageView backImgView, closeImgView;
    private MaterialToolbar toolbar;
    private CallbackManager callbackManager;
    private LoginButton loginFbbutton;
    private static final String EMAIL = "email";

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
        binding = ActivityLoginBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        initView();
        setLabel();

        //INITIALIXE LOGIN WITH FB

        appFunctions.storeData(Utils.FACEBOOK_APPID_KEY, "336068101395105");
        FacebookSdk.setApplicationId(appFunctions.retrieveValue(Utils.FACEBOOK_APPID_KEY));
        FacebookSdk.sdkInitialize(getApplicationContext());
        callbackManager = CallbackManager.Factory.create();

        loginFbbutton.setReadPermissions(Arrays.asList("public_profile"));
        //LoginManager.getInstance().logInWithReadPermissions(LoginActivity.this, Arrays.asList("public_profile"));

        // If you are using in a fragment, call loginButton.setFragment(this);
        // Callback registration

        loginFbbutton.registerCallback(callbackManager, new FacebookCallback<LoginResult>() {
            @Override
            public void onError(@NonNull FacebookException e) {
                appFunctions.showMessage( e.toString());
            }

            @Override
            public void onSuccess(LoginResult loginResult) {
                // App code
                AccessToken accessToken = AccessToken.getCurrentAccessToken();
                boolean isLoggedIn = accessToken != null && !accessToken.isExpired();
                GraphRequest request = GraphRequest.newMeRequest(
                        loginResult.getAccessToken(),
                        (me, response) -> {
                            if (response.getError() != null) {
                                // handle error
                                appFunctions.showMessage(response.getError().getErrorMessage());
                            } else {
                                try {
                                    FacebookSdk.sdkInitialize(getApplicationContext());
                                    LoginManager.getInstance().logOut();
                                    loginWithFacebook(me.toString());
                                } catch (Exception e) {
                                    appFunctions.showMessage("FB Error"+e.toString());
                                }
                            }
                        });
                Bundle parameters = new Bundle();
                parameters.putString("fields", "id,name,first_name,last_name,email,birthday,gender,location");
                request.setParameters(parameters);
                request.executeAsync();
            }

            @Override
            public void onCancel() {
                // App code
                appFunctions.showMessage("cancel");
            }

        });


    }


    private void initView() {
        contentView = binding.contentView;
        titleTxt = binding.mainToolbar.titleTxt;
        usernameTxtLayout = binding.usernameTxtLayout;
        usernameTxt = binding.usernametxt;
        passwordTxtLayout = binding.passwordTxtLayout;
        passwordTxt = binding.passwordTxt;
        loginBtn = binding.loginBtn;
        loginFbBtn = binding.loginFbBtn;
        loginFbbutton = binding.loginFbbutton;;
        forgotPasswordTxt = binding.forgotPasswordTxt;
        toolbar = findViewById(R.id.toolbar);

        loginBtn.setOnClickListener(new setOnClickAct());
        loginFbBtn.setOnClickListener(new setOnClickAct());
        forgotPasswordTxt.setOnClickListener(new setOnClickAct());
        usernameTxt.addTextChangedListener(new setOnTextChangeAct(usernameTxt));
        passwordTxt.addTextChangedListener(new setOnTextChangeAct(passwordTxt));
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });

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

            usernameTxtLayout.setHelperText(null);
            usernameTxtLayout.setHelperTextEnabled(false);
            usernameTxtLayout.setErrorEnabled(false);

            passwordTxtLayout.setHelperText(null);
            passwordTxtLayout.setHelperTextEnabled(false);
            passwordTxtLayout.setErrorEnabled(false);
        }

        @Override
        public void afterTextChanged(Editable s) {
            switch (editText.getId()) {

                case R.id.usernametxt:

                    if(usernameTxt.getText().length() == 0 ){

                        usernameTxtLayout.setErrorEnabled(true);
                        usernameTxtLayout.setHelperTextEnabled(true);
                        usernameTxtLayout.setHelperText("This field is required.");

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

            }
        }
    }




    public void logIn() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOGIN");
        parameters.put("username", usernameTxt.getText().toString() == null ? "" : usernameTxt.getText().toString());
        parameters.put("password", passwordTxt.getText().toString() == null ? "" : passwordTxt.getText().toString() );
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_login.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        try{

                            appFunctions.storeData(Utils.isUserLogIn, "1");
                            appFunctions.storeData(Utils.iMemberId, appFunctions.getJsonValue("iUserId", responseString));
                            appFunctions.storeData(Utils.USER_PROFILE_JSON, appFunctions.getJsonValue("result", responseString));
                            appFunctions.storeData(Utils.USER_FIRST_LOGIN, "1");

                            new StartActProcess(getActContext()).startAct(MainActivity.class);

                        }catch (Exception d){
                            appFunctions.showMessage(exeWebServer.toString());
                        }

                    }else{

                        if(appFunctions.getJsonValue("label", responseString).equalsIgnoreCase("email")){

                            usernameTxtLayout.setErrorEnabled(true);
                            usernameTxtLayout.setHelperTextEnabled(true);
                            usernameTxtLayout.setHelperText(appFunctions.getJsonValue("error", responseString));
                        }

                        if(appFunctions.getJsonValue("label", responseString).equalsIgnoreCase("password")){
                            passwordTxtLayout.setErrorEnabled(true);
                            passwordTxtLayout.setHelperTextEnabled(true);
                            passwordTxtLayout.setHelperText(appFunctions.getJsonValue("error", responseString));
                        }
                    }

                }

                //
            }
        });
        exeWebServer.execute();
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

                case R.id.loginBtn:

                    hideKeyboard((Activity) getActContext());

                    usernameTxtLayout.setHelperText(null);
                    usernameTxtLayout.setHelperTextEnabled(false);
                    usernameTxtLayout.setErrorEnabled(false);

                    passwordTxtLayout.setHelperText(null);
                    passwordTxtLayout.setHelperTextEnabled(false);
                    passwordTxtLayout.setErrorEnabled(false);

                    try{

                        logIn();
                    }catch (Exception e){
                        Toast.makeText(getActContext(), ""+e.toString(), Toast.LENGTH_SHORT).show();
                    }


                    break;
                case R.id.forgotPasswordTxt:

                    new StartActProcess(getActContext()).startAct(ForgotPasswordActivity.class);

                    break;

                case R.id.closeImgView:
                    onBackPressed();

                    break;

                case R.id.loginFbBtn:

                    loginFbbutton.performClick();

                    break;

            }
        }
    }

    public void loginWithFacebook(String data) {

        hideKeyboard((Activity) getActContext());

        appFunctions.showMessage(data);

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOGIN_WITH_FACEBOOK");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("firstName", appFunctions.getJsonValue("first_name",data));;
        parameters.put("lastName", appFunctions.getJsonValue("last_name", data));
        parameters.put("email", appFunctions.getJsonValue("email", data));
        parameters.put("mobileNumber", "");
        parameters.put("password",appFunctions.getJsonValue("id", data));
        parameters.put("displayPhoto", "https://graph.facebook.com/" +appFunctions.getJsonValue("id", data) + "/picture?type=large");
        parameters.put("loginType", "Facebook");
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_login_with_facebook.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        appFunctions.storeData(Utils.isUserLogIn, "1");
                        appFunctions.storeData(Utils.iMemberId, appFunctions.getJsonValue("iUserId", responseString));
                        appFunctions.storeData(Utils.USER_PROFILE_JSON, appFunctions.getJsonValue("result", responseString));
                        appFunctions.storeData(Utils.USER_FIRST_LOGIN, "1");

                        new StartActProcess(getActContext()).startAct(MainActivity.class);

                    }else{

                        appFunctions.showMessage(responseString);
                    }
                }
            }
        });
        exeWebServer.execute();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        callbackManager.onActivityResult(requestCode, resultCode, data);
    }

    public Context getActContext() {
        return LoginActivity.this;
    }
}