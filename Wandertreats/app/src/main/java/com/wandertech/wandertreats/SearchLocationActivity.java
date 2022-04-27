package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.widget.ImageView;
import android.widget.Toast;

import com.wandertech.wandertreats.adapter.PlacesAdapter;
import com.wandertech.wandertreats.databinding.ActivitySearchBinding;
import com.wandertech.wandertreats.databinding.ActivitySearchLocationBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Timer;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatEditText;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

public class SearchLocationActivity extends AppCompatActivity implements PlacesAdapter.setRecentLocClickList {

    private ActivitySearchLocationBinding binding;
    private View contentView;
    private GeneralFunctions appFunctions;
    private final Handler handler = new Handler();
    private AppCompatEditText searchTxt;
    private AppCompatImageView clearBtn;
    private RecyclerView resultsRecyclerList;
    private int claimType = 0;
    private String data = "";
    private long delay = 1500;
    private long searchdelay = 500;
    private long last_move = 0;
    private long last_search = 0;
    private Timer timer;
    private Handler latlonghandler = new Handler();
    private Handler searchhandler = new Handler();
    private ArrayList<HashMap<String, String>> placelist = new ArrayList<>();
    private PlacesAdapter placesAdapter;
    private ImageView imageCancel;
    private AppCompatImageView backImgView;
    private LinearLayoutCompat loadingLocationArea, noDataArea;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        claimType = getIntent().getIntExtra("SCAN_MODE", 0);
        data = getIntent().getStringExtra("result");

        binding = ActivitySearchLocationBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        backImgView = binding.toolbar.backImgView;
        searchTxt = binding.searchTxt;
        clearBtn = binding.clearBtn;
        loadingLocationArea = binding.loadingLocationArea;
        resultsRecyclerList = binding.resultsRecyclerList;
        noDataArea = binding.noDataArea;

        placesAdapter = new PlacesAdapter(getActContext(), placelist);
        resultsRecyclerList.setLayoutManager(new LinearLayoutManager(getActContext()));
        resultsRecyclerList .setAdapter(placesAdapter);
        placesAdapter.itemRecentLocClick(this);

        searchTxt.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                if(loadingLocationArea.getVisibility() == View.GONE){
                    loadingLocationArea.setVisibility(View.VISIBLE);
                }

            }

            @Override
            public void afterTextChanged(Editable s) {
                if (s.length() > 3) {
                    last_search = System.currentTimeMillis();
                    searchhandler.postDelayed(search_checker, delay);
                }else{
                    placelist.clear();
                }
            }
        });

        clearBtn.setOnClickListener(new setOnClickAct());
        backImgView.setOnClickListener(new setOnClickAct());

    }


    private Runnable search_checker = new Runnable() {
        public void run() {

            try {

                if (System.currentTimeMillis() > (last_search + delay - 500)) {
                    getPlaces(searchTxt.getText().toString());
                }

            }catch (Exception e){

            }

        }
    };

    public void getPlaces(String search){

        loadingLocationArea.setVisibility(View.VISIBLE);

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("ServiceType", "SEARCH_ADDRESS");
        parameters.put("input", search);
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_location.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {
                loadingLocationArea.setVisibility(View.GONE);
                //appFunctions.showMessage(responseString);
                if(responseString != null){

                    try{
                        JSONArray predictionsArr = appFunctions.getJsonArray("predictions", responseString);
                        if(predictionsArr != null && predictionsArr.length() > 0){
                            noDataArea.setVisibility(View.GONE);
                            resultsRecyclerList.setVisibility(View.VISIBLE);
                            placelist.clear();
//                        loaderSearch.setVisibility(View.GONE);

                            for (int i = 0; i < predictionsArr.length(); i++) {

                                JSONObject item = appFunctions.getJsonObject(predictionsArr, i);
                                HashMap<String, String> map = new HashMap<String, String>();
                                map.put("main_text", appFunctions.getJsonValue("main_text", item.toString()));
                                map.put("secondary_text", appFunctions.getJsonValue("secondary_text", item.toString()));
                                map.put("place_id", appFunctions.getJsonValue("place_id", item.toString()));
                                map.put("description", appFunctions.getJsonValue("description", item.toString()));
                                map.put("lat", appFunctions.getJsonValue("lat", item.toString()));
                                map.put("lng", appFunctions.getJsonValue("long", item.toString()));

                                placelist.add(map);

                            }


                            if (placelist.size() > 0) {

                                if (placesAdapter == null) {
                                    placesAdapter = new PlacesAdapter(getActContext(), placelist);
                                    resultsRecyclerList.setAdapter(placesAdapter);
                                    placesAdapter.itemRecentLocClick(SearchLocationActivity.this);

                                } else {
                                    placesAdapter.notifyDataSetChanged();

                                }
                            }


                        }else{
                            placelist.clear();
                            noDataArea.setVisibility(View.VISIBLE);
                            resultsRecyclerList.setVisibility(View.GONE);
                        }
                    }catch (Exception e){
                        appFunctions.showMessage(e.toString());
                    }



                }

            }
        });
        exeWebServer.execute();

    }

    @Override
    public void itemRecentLocClick(int position) {

        //appFunctions.showMessage(placelist.get(position).toString());

        Bundle bn = new Bundle();
        bn.putString("address", placelist.get(position).get("main_text"));
        bn.putString("lat", placelist.get(position).get("lat"));
        bn.putString("long", placelist.get(position).get("lng"));

        new StartActProcess(getActContext()).setOkResult(bn);
        finish();

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

                case R.id.backImgView:
                    onBackPressed();
                    break;

                case R.id.clearBtn:
                    searchTxt.setText("");
                    break;

                default:
                    break;

            }

        }

    }

    @Override
    public void onBackPressed() {
       // super.onBackPressed();

        Bundle bn = new Bundle();
        bn.putString("address", "");
        bn.putString("lat", "0");
        bn.putString("long","0");
        new StartActProcess(getActContext()).setOkResult(bn);
        finish();
    }

    public Context getActContext() {
        return SearchLocationActivity.this;
    }
}

