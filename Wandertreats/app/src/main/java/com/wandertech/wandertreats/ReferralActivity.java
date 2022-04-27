package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import com.wandertech.wandertreats.databinding.ActivityReferralBinding;
import com.wandertech.wandertreats.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;

import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;

public class ReferralActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ActivityReferralBinding binding;
    private AppCompatImageView backImgView;
    AppCompatTextView titleTxt;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);


        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityReferralBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        titleTxt = binding.toolbar.titleTxt;
        backImgView = binding.toolbar.backImgView;

        backImgView.setOnClickListener(new setOnClickAct());

        setLabel();

    }

    public void setLabel(){
        titleTxt.setText("Rewards");
    }

    private Context getActContext() {

        return ReferralActivity.this;
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
