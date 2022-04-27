package com.wandertech.wandertreats;

import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.view.View;
import android.view.WindowInsets;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;

import com.wandertech.wandertreats.databinding.ActivityLauncherBinding;
import com.wandertech.wandertreats.databinding.ActivityWelcomeBinding;
import com.wandertech.wandertreats.general.StartActProcess;

public class WelcomeActivity  extends AppCompatActivity {

    private ActivityWelcomeBinding binding;
    private View contentView;
    private final Handler handler = new Handler();
    private AppCompatButton loginBtn, registerBtn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        binding = ActivityWelcomeBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        contentView = binding.contentView;
        loginBtn = binding.loginBtn;
        registerBtn = binding.registerBtn;

        loginBtn.setOnClickListener(new setOnClickAct());
        registerBtn.setOnClickListener(new setOnClickAct());




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

                    new StartActProcess(getActContext()).startAct(LoginActivity.class);

                    break;
                case R.id.registerBtn:

                    new StartActProcess(getActContext()).startAct(RegisterActivity.class);

                    break;



            }

        }


    }


    public Context getActContext() {
        return WelcomeActivity.this;
    }
}

