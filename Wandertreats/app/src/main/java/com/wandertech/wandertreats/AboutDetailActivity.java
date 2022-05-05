package com.wandertech.wandertreats;

import android.content.Context;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.view.View;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;

import com.google.android.material.appbar.MaterialToolbar;
import com.wandertech.wandertreats.databinding.ActivityAboutBinding;
import com.wandertech.wandertreats.databinding.ActivityAboutDetailBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;

public class AboutDetailActivity extends AppCompatActivity {

    private ActivityAboutDetailBinding binding;

    private final Handler handler = new Handler();
    private GeneralFunctions appFunctions;
    private String packageVersion, packageVersionName;

    private MaterialToolbar toolbar;
    private AppCompatTextView titleTxt, contentText;
    private View contentView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityAboutDetailBinding.inflate(getLayoutInflater());

        setContentView(binding.getRoot());
        initView();
        setLabels();

    }

    private void setLabels() {

        titleTxt.setText(getIntent().getStringExtra("activity"));
        contentText.setText("Edit on admin");

    }

    private void initView() {

        toolbar = binding.mainToolbar.toolbar;
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

        titleTxt = binding.mainToolbar.titleTxt;
        contentText = binding.contentText;

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

            Bundle bn = new Bundle();

            switch(view.getId()){


                default:

                    break;



            }

        }


    }




    private Context getActContext() {

        return AboutDetailActivity.this;
    }

}

