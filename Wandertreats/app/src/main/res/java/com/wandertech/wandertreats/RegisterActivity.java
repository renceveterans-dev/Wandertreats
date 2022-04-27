package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatTextView;

import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.databinding.ActivityRegisterBinding;

public class RegisterActivity extends AppCompatActivity {

    private ActivityRegisterBinding binding;
    private View contentView;
    private final Handler handler = new Handler();
    private AppCompatTextView titleTxt;
    private TextInputLayout usernameTxtLayout, passwordTxtLayout;
    private TextInputEditText usernameTxt, passwordTxt;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        binding = ActivityRegisterBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());


//        initView();
//        setLabel();




    }

//    private void initView() {
//        contentView = binding.contentView;
//        titleTxt = binding.toolbar.titleTxt;
//        usernameTxtLayout = binding.usernameTxtLayout;
//        usernameTxt = binding.usernametxt;
//        passwordTxtLayout = binding.passwordTxtLayout;
//        passwordTxt = binding.passwordTxt;
//
//        usernameTxt.addTextChangedListener(new setOnTextChangeAct(usernameTxt));
//        passwordTxt.addTextChangedListener(new setOnTextChangeAct(passwordTxt));
//
//    }
//
//    private void setLabel() {
//
//        titleTxt.setText("Log In");
//    }

//    public class setOnTextChangeAct implements TextWatcher {
//        public TextInputEditText editText;
//        public setOnTextChangeAct(TextInputEditText editText){
//            this.editText = editText;
//        }
//        @Override
//        public void beforeTextChanged(CharSequence s, int start, int count, int after) {
//        }
//        @Override
//        public void onTextChanged(CharSequence s, int start, int before, int count) {
//
//            usernameTxtLayout.setHelperText(null);
//            usernameTxtLayout.setHelperTextEnabled(false);
//            usernameTxtLayout.setErrorEnabled(false);
//
//            passwordTxtLayout.setHelperText(null);
//            passwordTxtLayout.setHelperTextEnabled(false);
//            passwordTxtLayout.setErrorEnabled(false);
//        }
//
//        @Override
//        public void afterTextChanged(Editable s) {
//            switch (editText.getId()) {
//
//                case R.id.usernametxt:
//
//                    if(usernameTxt.getText().length() == 0 ){
//
//                        usernameTxtLayout.setErrorEnabled(true);
//                        usernameTxtLayout.setHelperTextEnabled(true);
//                        usernameTxtLayout.setHelperText("This field is required.");
//
//                        return;
//                    }
//
//                    break;
//
//                case R.id.passwordTxt:
//
//                    if(passwordTxt.getText().length() == 0 ){
//
//                        passwordTxtLayout.setErrorEnabled(true);
//                        passwordTxtLayout.setHelperTextEnabled(true);
//                        passwordTxtLayout.setHelperText("This field is required.");
//
//                        return;
//                    }
//
////                    usernameTxtLayout.setHelperText(null);
////                    usernameTxtLayout.setHelperTextEnabled(false);
////                    usernameTxtLayout.setErrorEnabled(false);
////
////                    passwordTxtLayout.setHelperText(null);
////                    passwordTxtLayout.setHelperTextEnabled(false);
////                    passwordTxtLayout.setErrorEnabled(false);
//
//                    break;
//
//            }
//        }
//    }
//


    public Context getActContext() {
        return RegisterActivity.this;
    }
}


