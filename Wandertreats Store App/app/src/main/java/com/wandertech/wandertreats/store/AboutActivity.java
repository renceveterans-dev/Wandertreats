package com.wandertech.wandertreats.store;

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
import com.wandertech.wandertreats.store.AboutDetailActivity;
import com.wandertech.wandertreats.store.MyApp;
import com.wandertech.wandertreats.store.databinding.ActivityAboutBinding;
import com.wandertech.wandertreats.store.databinding.ActivityClaimBinding;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.StartActProcess;

public class AboutActivity  extends AppCompatActivity{

    private ActivityAboutBinding binding;

    private final Handler handler = new Handler();
    private com.wandertech.wandertreats.store.general.GeneralFunctions appFunctions;
    private String packageVersion, packageVersionName;

    private MaterialToolbar toolbar;
    private AppCompatTextView titleTxt, appName, appVersiom;
    private View contentView;
    private LinearLayoutCompat privacyPolicyArea, aboutUsArea, termsAndConditionArea;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityAboutBinding.inflate(getLayoutInflater());

        setContentView(binding.getRoot());
        initView();
        setLabels();

    }

    private void setLabels() {

        try {
            PackageInfo pInfo = getActContext().getPackageManager().getPackageInfo(getActContext().getPackageName(), 0);
            packageVersion = pInfo.versionName;
        } catch (PackageManager.NameNotFoundException e) {
            e.printStackTrace();
        }

        titleTxt.setText("About");
        appName.setText(getActContext().getString(R.string.app_name));
        appVersiom.setText("v"+packageVersion);
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
        appName = binding.appName;
        appVersiom = binding.appVersiom;

        aboutUsArea = binding.aboutArea;
        termsAndConditionArea = binding.termsAndConditionArea;
        privacyPolicyArea = binding.privacyPolicyArea;

        aboutUsArea.setOnClickListener(new setOnClickAct());
        termsAndConditionArea.setOnClickListener(new setOnClickAct());
        privacyPolicyArea.setOnClickListener(new setOnClickAct());
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


                case R.id.aboutArea:

                    bn.putString("activity", "About");
                    new StartActProcess(getActContext()).startActWithData(AboutDetailActivity.class, bn);
                    break;
                case R.id.termsAndConditionArea:

                    bn.putString("activity", "Terms and Condition");
                    new StartActProcess(getActContext()).startActWithData(AboutDetailActivity.class, bn);
                    break;

                case R.id.privacyPolicyArea:

                    bn.putString("activity", "Privacy Policy");
                    new StartActProcess(getActContext()).startActWithData(AboutDetailActivity.class, bn);
                    break;
                default:

                    break;



            }

        }


    }




    private Context getActContext() {

        return AboutActivity.this;
    }

}

