package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Patterns;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatTextView;

import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.databinding.ActivityAddEmailBinding;
import com.wandertech.wandertreats.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;

public class AddEmailActivity  extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow;
    private ActivityAddEmailBinding binding;

    private TextInputEditText emailTxt;
    private TextInputLayout emailTxtLayout;
    private AppCompatButton continueBtn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityAddEmailBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        setView();

    }

    private void setView() {

        emailTxt = binding.emailTxt;
        emailTxtLayout = emailTxtLayout;
        continueBtn = binding.continueBtn;

        emailTxt.addTextChangedListener(new setOnTextChangeAct(emailTxt));
        continueBtn.setOnClickListener(new setOnClickAct());
    }


    private Context getActContext() {

        return AddEmailActivity.this;
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

            switch (editText.getId()) {
                case R.id.emailTxt:

                    emailTxtLayout.setHelperText(null);
                    emailTxtLayout.setHelperTextEnabled(false);
                    emailTxtLayout.setErrorEnabled(false);

                    break;
            }
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

                    if(emailTxt.getText().length() > 0  && !Patterns.EMAIL_ADDRESS.matcher(emailTxt.getText().toString().trim()).matches() ){
                        if(!emailTxtLayout.isErrorEnabled()){
                            emailTxtLayout.setErrorEnabled(true);
                            emailTxtLayout.setHelperTextEnabled(true);
                            emailTxtLayout.setHelperText("Invalid email.");
                        }
                    }
                    break;

            }
        }
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){

                case R.id.continueBtn:



                default:
                    break;



            }

        }


    }
}
