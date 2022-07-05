package com.wandertech.wandertreats.store;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatTextView;

import com.wandertech.wandertreats.store.databinding.ActivityPreRegisterBinding;
import com.wandertech.wandertreats.store.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.store.general.GeneralFunctions;

public class PreRegisterActivity extends AppCompatActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow;
    private ActivityPreRegisterBinding binding;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityPreRegisterBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());


        initView();
    }


    private void initView() {

//        contentView = binding.contentView;
//        toolbar = binding.mainToolbar.toolbar;
//        titleTxt = binding.mainToolbar.titleTxt;
//        backImgView = binding.mainToolbar.backImgView;
//        // backImgView.setBackgroundResource(R.drawable.close);
//
//        fNameTxt = binding.fNameTxt;
//        lNameTxt = binding.lNameTxt;
//        emailTxt = binding.emailTxt;
//        mobileTxt = binding.mobileTxt;
//        passwordTxt = binding.passwordTxt;
//        rePasswordTxt = binding.rePasswordTxt;
//
//        fNameTxtLayout = binding.fNameTxtLayout;
//        lNameTxtLayout = binding.lNameTxtLayout;
//        emailTxtLayout = binding.emailTxtLayout;
//        mobileTxtLayout = binding.mobileTxtLayout;
//        passwordTxtLayout = binding.passwordTxtLayout;
//        rePasswordTxtLayout = binding.rePasswordTxtLayout;
//
//        registerBtn = binding.registerBtn;
//        loginBtn = binding.loginBtn;
//
//        fNameTxt.addTextChangedListener(new RegisterActivity.setOnTextChangeAct(fNameTxt));
//        lNameTxt.addTextChangedListener(new RegisterActivity.setOnTextChangeAct(lNameTxt));
//        emailTxt.addTextChangedListener(new RegisterActivity.setOnTextChangeAct(emailTxt));
//        mobileTxt.addTextChangedListener(new RegisterActivity.setOnTextChangeAct(mobileTxt));
//        passwordTxt.addTextChangedListener(new RegisterActivity.setOnTextChangeAct(passwordTxt));
//        rePasswordTxt.addTextChangedListener(new RegisterActivity.setOnTextChangeAct(rePasswordTxt));
//
//        registerBtn.setOnClickListener(new RegisterActivity.setOnClickAct());
//        loginBtn.setOnClickListener(new RegisterActivity.setOnClickAct());
//
//        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View view) {
//                onBackPressed();
//            }
//        });

    }


    private Context getActContext() {

        return PreRegisterActivity.this;
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){



                default:
                    break;



            }

        }


    }
}
