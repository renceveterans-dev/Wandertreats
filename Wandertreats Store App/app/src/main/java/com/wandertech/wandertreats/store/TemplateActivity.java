package com.wandertech.wandertreats.store;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import com.wandertech.wandertreats.store.MyApp;
import com.wandertech.wandertreats.store.databinding.ActivitySavedAddressBinding;
import com.wandertech.wandertreats.store.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.store.general.GeneralFunctions;

import androidx.appcompat.widget.AppCompatTextView;

public class TemplateActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow;
    private ActivityTemplateBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityTemplateBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

    }


    private Context getActContext() {

        return TemplateActivity.this;
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
