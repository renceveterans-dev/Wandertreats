package com.wandertech.wandertreats.store.main.home;

import android.app.Activity;
import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.os.SystemClock;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.view.WindowManager;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatEditText;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.core.widget.NestedScrollView;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProvider;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import okhttp3.internal.Util;

import com.facebook.shimmer.ShimmerFrameLayout;
import com.wandertech.wandertreats.store.LocationPickerActivity;
import com.wandertech.wandertreats.store.LoginActivity;
import com.wandertech.wandertreats.store.MainActivity;
import com.wandertech.wandertreats.store.MerchantListActivity;
import com.wandertech.wandertreats.store.MyApp;
import com.wandertech.wandertreats.store.NotificationActivity;
import com.wandertech.wandertreats.store.ProductActivity;
import com.wandertech.wandertreats.store.R;
import com.wandertech.wandertreats.store.RegisterActivity;
import com.wandertech.wandertreats.store.ScanActivity;
import com.wandertech.wandertreats.store.SearchActivity;
import com.wandertech.wandertreats.store.StoreActivity;
import com.wandertech.wandertreats.store.adapter.GridCategoryAdapter;
import com.wandertech.wandertreats.store.adapter.MainAdapter;
import com.wandertech.wandertreats.store.adapter.MainItemAdapter;
import com.wandertech.wandertreats.store.adapter.MainItemListAdapter;
import com.wandertech.wandertreats.store.databinding.FragmentHomeBinding;
import com.wandertech.wandertreats.store.general.Data;
import com.wandertech.wandertreats.store.general.EndlessRecyclerOnScrollListener;
import com.wandertech.wandertreats.store.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.MockData;
import com.wandertech.wandertreats.store.general.PopUpDialog;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.model.ItemModel;
import com.wandertech.wandertreats.store.model.ParentModel;
import com.wandertech.wandertreats.store.utils.Constants;
import com.wandertech.wandertreats.store.utils.Utils;
import com.wang.avi.AVLoadingIndicatorView;

import java.util.ArrayList;
import java.util.HashMap;

public class HomeFragment extends Fragment implements GridCategoryAdapter.ItemOnClickListener {

    private HomeViewModel homeViewModel;
    private FragmentHomeBinding binding;
    private ArrayList<ParentModel> mainArr = new ArrayList<>();
    private ArrayList<HashMap<String, String>> catArrList = new ArrayList<>();
    private ArrayList<HashMap<String, String>> prodArrList = new ArrayList<>();
    private ArrayList<ItemModel> productArrayList = new ArrayList<>();
    private RecyclerView mainRecyclerList;
    private MainAdapter mainAdapter;
    private MainItemListAdapter mainItemListAdapter;
    private GridCategoryAdapter gridCategoryAdapter;
    private TextView textView;
    private RecyclerView categoryRecyclerList, mainRecylerList, prodRecyclerList;
    private AppCompatImageView scanBtn, achorDownIcon;
    private AppCompatTextView locationTxt;
    private ViewGroup mContainer;
    private AppCompatTextView searchTxt;
    private MainActivity mainActivity;
    private LinearLayoutCompat locationArea;
    private GeneralFunctions appFunctions;
    private AppCompatTextView greetingsTxt;
    private AVLoadingIndicatorView loadingLocation;
    private String profileData = "";
    private ShimmerFrameLayout loaderShimmer;
    private AppCompatImageView notificationBtn;
    private TextView badgeTextView;
    private View dropShadow;
    private NestedScrollView mainScollView;
    public View root;

    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        appFunctions = MyApp.getInstance().getGeneralFun(container.getContext());

        homeViewModel = new ViewModelProvider(this).get(HomeViewModel.class);
        binding = FragmentHomeBinding.inflate(inflater, container, false);
        mContainer = container;
        root = binding.getRoot();

        if(getActivity() != null && isAdded()) {

            profileData = appFunctions.retrieveValue(Utils.USER_PROFILE_JSON);

            textView = binding.textHome;
            greetingsTxt = binding.greetingsTxt;
            notificationBtn = binding.notificationBtn;
            mainScollView = binding.mainScollView;
            categoryRecyclerList = binding.categoryRecyclerList;
            mainRecyclerList = binding.mainRecyclerList;
            prodRecyclerList = binding.prodRecyclerList;
            searchTxt = binding .searchTxt;
            scanBtn = binding.scanBtn;

            loaderShimmer = binding.loaderShimmer;
            dropShadow = binding.dropShadow;
            locationArea = binding.locationArea;
            loadingLocation = binding.loadingLocation;
            achorDownIcon = binding.achorDownIcon;
            locationTxt = binding.locationTxt;

            badgeTextView = binding.notificationBadge.badgeTextView;

        }

        setListner();


        return root;
    }

    private void setListner() {
        if(getActivity() != null) {

            getActivity().runOnUiThread(new Runnable() {
                public void run() {

                    notificationBtn.setOnClickListener(new setOnClickAct());
                    searchTxt.setOnClickListener(new setOnClickAct());
                    scanBtn.setOnClickListener(new setOnClickAct());
                    locationArea.setOnClickListener(new setOnClickAct());



                    ((MainActivity)getActivity()).setLocationListener(new MainActivity.LocationListener() {
                        @Override
                        public void onLocationFound(String address, double latitude, double longitude) {

                            try {
                                appFunctions.storeData(Utils.CURRENT_ADDRESSS, address);
                                appFunctions.storeData(Utils.CURRENT_LATITUDE, latitude+ "");
                                appFunctions.storeData(Utils.CURRENT_LONGITUDE, longitude + "");
                                locationTxt.setText(appFunctions.retrieveValue(Utils.CURRENT_ADDRESSS));
                                loadingLocation.setVisibility(View.GONE);
                                achorDownIcon.setVisibility(View.VISIBLE);
                                locationTxt.setVisibility(View.VISIBLE);
                                loadData();
                            }catch (Exception e){
                                appFunctions.showMessage(e.toString());
                            }
                        }
                    });

                    try{
                        locationTxt.setText(appFunctions.retrieveValue(Utils.CURRENT_ADDRESSS));
                        loadingLocation.setVisibility(View.GONE);
                        achorDownIcon.setVisibility(View.VISIBLE);
                        locationTxt.setVisibility(View.VISIBLE);
                    }catch (Exception e){

                    }

                    loadData();

                    mainScollView.getViewTreeObserver().addOnScrollChangedListener(new ViewTreeObserver.OnScrollChangedListener() {
                        @Override
                        public void onScrollChanged() {
                            View view = (View) mainScollView.getChildAt(mainScollView.getChildCount() - 1);

                            int diff = (view.getBottom() - (mainScollView.getHeight() + mainScollView.getScrollY()));

                            if(mainScollView.getScrollY() == 0){
                                ///  dropShadow.setVisibility(View.GONE);
                            }else{
                                // dropShadow.setVisibility(View.VISIBLE);
                            }
                        }
                    });
                }
            });


        }


    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        if(getActivity() !=null){
            //do stuff
            greetingsTxt.setText("Hi "+appFunctions.getJsonValue("vName", profileData)+"!");
        }



    }

    public void loadData() {
        loaderShimmer.setVisibility(View.VISIBLE);

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_DATA");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("latitude", appFunctions.retrieveValue(Utils.CURRENT_LATITUDE));
        parameters.put("longitude", appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE));
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_home.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        loaderShimmer.setVisibility(View.GONE);

                        try{
                            catArrList.clear();
                            catArrList = Data.getMerchantTypeData(appFunctions.getJsonArray("merchantsTypes", responseString), appFunctions);
                            gridCategoryAdapter = new GridCategoryAdapter(getActivity().getApplicationContext(), catArrList, false);
                            GridLayoutManager mGridManager = new GridLayoutManager(getActivity().getApplicationContext(), 3);
                            categoryRecyclerList.setLayoutManager(mGridManager);
                            categoryRecyclerList.setAdapter( gridCategoryAdapter );
                            gridCategoryAdapter.setOnItemClick(HomeFragment.this::setOnItemClick);

                            mainArr.clear();
                            mainArr = Data.getParentData(appFunctions.getJsonArray("featured", responseString), appFunctions);
                            mainAdapter = new MainAdapter(mainArr, getActivity().getApplicationContext(), appFunctions);
                            mainRecyclerList.setLayoutManager(new LinearLayoutManager(getActivity().getApplicationContext(), LinearLayoutManager.VERTICAL, false));
                            mainRecyclerList.setNestedScrollingEnabled(false);
                            mainRecyclerList.setAdapter(mainAdapter);

                            if(Integer.parseInt(appFunctions.getJsonValue("notificationCount", responseString)) > 0 ){
                                badgeTextView.setText(appFunctions.getJsonValue("notificationCount", responseString));
                                badgeTextView.setVisibility(View.VISIBLE);
                            }else{
                                badgeTextView.setVisibility(View.GONE);
                            }

                            //appFunctions.showMessage(appFunctions.getJsonValue("notificationCount", responseString));


//                            productArrayList.clear();
//                            Toast.makeText(getActContext(),  Data.getProductData(appFunctions.getJsonArray("productList", responseString), appFunctions).toString(), Toast.LENGTH_SHORT).show();
//                            productArrayList = Data.getProductData(appFunctions.getJsonArray("productList", responseString), appFunctions);
//                            mainItemListAdapter = new MainItemListAdapter(productArrayList, getContext().getApplicationContext());
//                            prodRecyclerList.setLayoutManager(new LinearLayoutManager(getActContext(), LinearLayoutManager.VERTICAL, false));
//                            prodRecyclerList.setNestedScrollingEnabled(false);
//                            prodRecyclerList.setAdapter(mainItemListAdapter);


                        }catch (Exception e){
                            //Toast.makeText(getActContext(), "Exception sdsdsd : "+e.toString(), Toast.LENGTH_SHORT).show();
                        }



                    }else{
                        //Toast.makeText(getActContext(), "Error "+ responseString, Toast.LENGTH_SHORT).show();
                    }
                }else{
                    Toast.makeText(getActContext(), "Error "+ responseString, Toast.LENGTH_SHORT).show();
                }

            }
        });
        exeWebServer.execute();
    }

    public Context getActContext(){
        return  getActivity();
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        binding = null;
    }

    @Override
    public void onResume() {
        super.onResume();
//        try{
//
//            if(!appFunctions.retrieveValue(Utils.CURRENT_ADDRESSS).equalsIgnoreCase("") || appFunctions.retrieveValue(Utils.CURRENT_ADDRESSS) != null){
//                //loadingLocation.setVisibility(View.VISIBLE);
//                locationTxt.setText(appFunctions.retrieveValue(Utils.CURRENT_ADDRESSS));
//            }else{
//                loadingLocation.setVisibility(View.GONE);
//            }
//        }catch (Exception e){
//
//        }
    }

    @Override
    public void setOnItemClick(int position) {
        try{
            Bundle bn = new Bundle();
            bn.putString("merchantType", catArrList.get(position).get("vMerchantType"));
            new StartActProcess(getActContext()).startActWithData(MerchantListActivity.class, bn);
        }catch (Exception e){
            appFunctions.showMessage(e.toString());
        }

    }

    public class setOnClickAct implements View.OnClickListener {

        private static final long MIN_CLICK_INTERVAL=600;
        private long mLastClickTime;

        @Override
        public void onClick(View view) {

            Bundle bn = new Bundle();

            long currentClickTime= SystemClock.uptimeMillis();
            long elapsedTime=currentClickTime-mLastClickTime;

            mLastClickTime=currentClickTime;

            if(elapsedTime<=MIN_CLICK_INTERVAL)
                return;

            switch(view.getId()){

                case R.id.searchTxt:
                    new StartActProcess(getActContext()).startAct(SearchActivity.class);


                    break;
                case R.id.notificationBtn:

                    new StartActProcess(getActContext()).startAct(NotificationActivity.class);

                    break;
                case R.id.registerBtn:

                    new StartActProcess(getActContext()).startAct(RegisterActivity.class);

                    break;
                case R.id.homeBtn:

                    new StartActProcess(getActContext()).startAct(MainActivity.class);

                    break;

                case R.id.storeBtn:
                    new StartActProcess(getActContext()).startAct(StoreActivity.class);

                    break;
                case R.id.scanBtn:


                    bn.putInt("SCAN_MODE", 11);
                    new StartActProcess(getActContext()).startActWithData(ScanActivity.class, bn);

                    break;

                case R.id.locationArea:

                    new StartActProcess(getActContext()).startActForResult(LocationPickerActivity.class, bn, Constants.LOCATION_PICKER);

                    break;

            }

        }
    }


}