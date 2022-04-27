package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import com.wandertech.wandertreats.databinding.ActivityPurchasePreviewBinding;
import com.wandertech.wandertreats.databinding.ActivitySavedAddressBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;

import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.recyclerview.widget.RecyclerView;

public class SaveAddressActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView titleTxt;
    private ImageView backArrow;
    private ActivitySavedAddressBinding binding;
    private RecyclerView addressRecyclerList;
    AppCompatImageView backImgView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivitySavedAddressBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        titleTxt = binding.toolbar.titleTxt;
        backImgView = binding.toolbar.backImgView;
        addressRecyclerList = binding.addressRecyclerList;

        backImgView.setOnClickListener(new setOnClickAct());

        setLabel();

    }

    public void setLabel(){
        titleTxt.setText("Address");
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }

    private Context getActContext() {

        return SaveAddressActivity.this;
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){

                case R.id.backImgView:

                    onBackPressed();
                    break;

                default:
                    break;



            }

        }


    }
}
