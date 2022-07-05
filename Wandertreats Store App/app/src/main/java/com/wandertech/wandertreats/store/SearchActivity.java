package com.wandertech.wandertreats.store;;

import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.Toast;

import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.store.adapter.MainAdapter;
import com.wandertech.wandertreats.store.databinding.ActivityClaimBinding;
import com.wandertech.wandertreats.store.databinding.ActivitySearchBinding;
import com.wandertech.wandertreats.store.general.Data;
import com.wandertech.wandertreats.store.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.model.ParentModel;
import com.wandertech.wandertreats.store.utils.Utils;

import java.util.ArrayList;
import java.util.HashMap;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatEditText;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

public class SearchActivity  extends AppCompatActivity {

    private ActivitySearchBinding binding;
    private View contentView;
    private GeneralFunctions appFunctions;
    private final Handler handler = new Handler();
    private AppCompatEditText searchTxt;
    private AppCompatImageView scanBtn;
    private RecyclerView resultsRecyclerList;
    private LinearLayoutCompat loadingLocationArea, noResultsArea;
    private int claimType = 0;
    private String data = "";
    private long delay = 1000;
    private long last_text_edit = 0;
    private Handler searchHandler = new Handler();
    private ArrayList<ParentModel> mainArr = new ArrayList<>();
    private MainAdapter mainAdapter;
    private MaterialToolbar toolbar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        claimType = getIntent().getIntExtra("SCAN_MODE", 0);
        data = getIntent().getStringExtra("result");

        binding = ActivitySearchBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        toolbar = binding.mainToolbar.toolbar;
        searchTxt = binding.searchTxt;
        scanBtn = binding.scanBtn;
        resultsRecyclerList = binding.resultsRecyclerList;
        loadingLocationArea = binding.loadingLocationArea;
        noResultsArea = binding.noResultsArea;


        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

        searchTxt.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                searchHandler.removeCallbacks(search_finish_checker);
                loadingLocationArea.setVisibility(View.VISIBLE);
                resultsRecyclerList.setVisibility(View.GONE);
            }

            @Override
            public void afterTextChanged(Editable s) {
                if (s.length() > 0) {
                    loadingLocationArea.setVisibility(View.VISIBLE);
                    resultsRecyclerList.setVisibility(View.GONE);
                    last_text_edit = System.currentTimeMillis();
                    searchHandler.postDelayed(search_finish_checker, delay);
                } else {

                }
            }
        });
    }



    private Runnable search_finish_checker = new Runnable() {
        public void run() {
            if (System.currentTimeMillis() > (last_text_edit + delay - 500)) {
                //appFunctions.showMessage(searchTextView.getText().toString().trim());
                hideKeyboard();
                loadSearch();

            }
        }
    };

    private void hideKeyboard(){

        InputMethodManager inputManager = (InputMethodManager) getActContext().getSystemService(Context.INPUT_METHOD_SERVICE);
        inputManager.hideSoftInputFromWindow(this.getCurrentFocus().getWindowToken(),InputMethodManager.HIDE_NOT_ALWAYS);

    }

    public void loadSearch() {

        loadingLocationArea.setVisibility(View.VISIBLE);

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_SEARCH");
        parameters.put("keyword",   searchTxt.getText().toString() == null ? "" :   searchTxt.getText().toString());
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_search.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false, appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                loadingLocationArea.setVisibility(View.GONE);
                resultsRecyclerList.setVisibility(View.VISIBLE);

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        int resultsCount = Integer.parseInt(appFunctions.getJsonValue("productResultCount", responseString));

                        if(resultsCount > 0){

                            mainArr.clear();
                            mainArr = Data.getParentData(appFunctions.getJsonArray("featured", responseString), appFunctions);

                            if(mainArr.size() > 0){

                                noResultsArea.setVisibility(View.GONE);
                                loadingLocationArea.setVisibility(View.GONE);
                                resultsRecyclerList.setVisibility(View.VISIBLE);

                                mainAdapter = new MainAdapter(mainArr, getActContext().getApplicationContext(), appFunctions);
                                resultsRecyclerList.setLayoutManager(new LinearLayoutManager(getActContext().getApplicationContext(), LinearLayoutManager.VERTICAL, false));
                                resultsRecyclerList.setNestedScrollingEnabled(false);
                                resultsRecyclerList.setAdapter(mainAdapter);

                            }else{
                                noResultsArea.setVisibility(View.VISIBLE);
                                loadingLocationArea.setVisibility(View.GONE);
                                resultsRecyclerList.setVisibility(View.GONE);


                            }
                        }else{

                            noResultsArea.setVisibility(View.VISIBLE);
                            loadingLocationArea.setVisibility(View.GONE);
                            resultsRecyclerList.setVisibility(View.GONE);
                        }

                    }
                }

            }
        });
        exeWebServer.execute();
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

                default:
                    break;



            }

        }


    }



    public Context getActContext() {
        return SearchActivity.this;
    }
}

