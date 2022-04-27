package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.view.WindowInsets;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.wandertech.wandertreats.databinding.ActivityLauncherBinding;
import com.wandertech.wandertreats.general.StartActProcess;

public class LauncherActivity extends AppCompatActivity {

    private ActivityLauncherBinding binding;
    private View contentView;
    private final Handler handler = new Handler();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        binding = ActivityLauncherBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        contentView = binding.contentView;

        if (Build.VERSION.SDK_INT >= 30) {
            contentView.getWindowInsetsController().hide(
                    WindowInsets.Type.statusBars() | WindowInsets.Type.navigationBars());
        } else {
            // Note that some of these constants are new as of API 16 (Jelly Bean)
            // and API 19 (KitKat). It is safe to use them, as they are inlined
            // at compile-time and do nothing on earlier devices.
            contentView.setSystemUiVisibility(View.SYSTEM_UI_FLAG_LOW_PROFILE
                    | View.SYSTEM_UI_FLAG_FULLSCREEN
                    | View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                    | View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY
                    | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                    | View.SYSTEM_UI_FLAG_HIDE_NAVIGATION);
        }


        Toast.makeText(LauncherActivity.this, "AHAHAH", Toast.LENGTH_SHORT).show();
        handler.postDelayed(new Runnable() {

            @Override
            public void run() {
                new StartActProcess(getActContext()).startAct(WelcomeActivity.class);
            }
        }, 1500);
//
//

    }


        public Context getActContext() {
            return LauncherActivity.this;
        }
}

