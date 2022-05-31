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
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;

public class AboutDetailActivity extends AppCompatActivity {

    private ActivityAboutDetailBinding binding;

    private final Handler handler = new Handler();
    private GeneralFunctions appFunctions;
    private String packageVersion, packageVersionName;

    private MaterialToolbar toolbar;
    private AppCompatTextView titleTxt, contentText;
    private View contentView;
    private String generalData;
    public JSONArray configArr = new JSONArray();

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

        generalData = appFunctions.retrieveValue(Utils.APP_GENERAL_DATA);
        configArr = appFunctions.getJsonArray("about", generalData);

        //appFunctions.showMessage(  configArr.toString());

        if(getIntent().getStringExtra("activity").equalsIgnoreCase("About")){
            contentText.setText(appFunctions.getJsonValue("vConfigValue", appFunctions.getJsonObject(configArr, 0).toString()));

        }else if(getIntent().getStringExtra("activity").equalsIgnoreCase("Terms and Condition")){
            contentText.setText(appFunctions.getJsonValue("vConfigValue", appFunctions.getJsonObject(configArr, 1).toString()));
        }else if(getIntent().getStringExtra("activity").equalsIgnoreCase("Privacy Policy")) {
            contentText.setText(appFunctions.getJsonValue("vConfigValue", appFunctions.getJsonObject(configArr, 2).toString()));
        }
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

