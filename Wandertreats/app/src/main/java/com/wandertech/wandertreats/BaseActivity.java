package com.wandertech.wandertreats;

import android.os.Bundle;
import android.util.Log;

import com.wandertech.wandertreats.general.StartActProcess;

import androidx.appcompat.app.AppCompatActivity;

public class BaseActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Thread.setDefaultUncaughtExceptionHandler(new Thread.UncaughtExceptionHandler() {
            public void uncaughtException(Thread paramThread, Throwable paramThrowable) {
                Log.e("Error"+Thread.currentThread().getStackTrace()[2],paramThrowable.getLocalizedMessage());
                new StartActProcess(BaseActivity.this).startAct(MainActivity.class);
//                finish();
            }
        });
    }
}