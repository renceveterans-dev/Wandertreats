package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Toast;

import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.facebook.shimmer.ShimmerFrameLayout;
import com.google.android.material.appbar.MaterialToolbar;
import com.wandertech.wandertreats.adapter.GridCategoryAdapter;
import com.wandertech.wandertreats.adapter.MainAdapter;
import com.wandertech.wandertreats.adapter.MerchantAdapter;
import com.wandertech.wandertreats.databinding.ActivityMerchantsListBinding;
import com.wandertech.wandertreats.databinding.ActivityNotificationBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.utils.Utils;

import java.util.ArrayList;
import java.util.HashMap;

public class MerchantListActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ActivityMerchantsListBinding binding;
    public ArrayList<HashMap<String, String>> merArrList = new ArrayList<>();
    private AppCompatTextView titleTxt;
    private String merchantType = "";
    private ShimmerFrameLayout loaderShimmer;
    private MerchantAdapter merchantAdapter;
    private RecyclerView merchantRecyclerView;
    private LinearLayoutCompat noDataArea;
    private MaterialToolbar toolbar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityMerchantsListBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        try{
            merchantType = getIntent().getStringExtra("merchantType");
        }catch (Exception e){
            appFunctions.showMessage(e.toString());
        }

       // appFunctions.showMessage(merchantType);

        titleTxt = binding.mainToolbar.titleTxt;
        toolbar = binding.mainToolbar.toolbar;
        loaderShimmer = binding.loaderShimmer;
        merchantRecyclerView = binding.merchantRecyclerView;
        noDataArea = binding.noDataArea;

        titleTxt.setText("Merchants #"+ merchantType);
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

        loadData();

    }

    public void loadData() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_MERCHANTS");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("latitude", appFunctions.retrieveValue(Utils.CURRENT_LATITUDE));
        parameters.put("longitude", appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE));
        parameters.put("longitude", appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE));
        parameters.put("merchantType", merchantType);
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_merchants.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        try{
                            loaderShimmer.setVisibility(View.GONE);
                            merArrList = Data.getMerchantData(appFunctions.getJsonArray("data", responseString), appFunctions);

                            if(merArrList.size()>0){
                                noDataArea.setVisibility(View.GONE);
                                merchantRecyclerView.setVisibility(View.VISIBLE);

                                merchantAdapter = new MerchantAdapter(getActContext(), merArrList, false);
                                merchantRecyclerView.setLayoutManager(new LinearLayoutManager(getActContext(), LinearLayoutManager.VERTICAL, false));
                                merchantRecyclerView.setAdapter(merchantAdapter);

                            }else{
                                noDataArea.setVisibility(View.VISIBLE);
                                merchantRecyclerView.setVisibility(View.GONE);
                            }
                          //  appFunctions.showMessage(merArrList.toString());



                        }catch (Exception e){
                            appFunctions.showMessage(e.toString());
                        }





                    }else{
                        Toast.makeText(getActContext(), "Error "+ responseString, Toast.LENGTH_SHORT).show();
                    }
                }else{
                    Toast.makeText(getActContext(), "Error "+ responseString, Toast.LENGTH_SHORT).show();
                }

            }
        });
        exeWebServer.execute();
    }


    private Context getActContext() {

        return MerchantListActivity.this;
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
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
