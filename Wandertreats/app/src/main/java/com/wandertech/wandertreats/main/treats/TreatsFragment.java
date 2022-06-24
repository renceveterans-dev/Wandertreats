package com.wandertech.wandertreats.main.treats;

import android.app.Activity;
import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.os.SystemClock;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.FrameLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProvider;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.facebook.shimmer.ShimmerFrameLayout;
import com.google.android.material.tabs.TabItem;
import com.google.android.material.tabs.TabLayout;
import com.wandertech.wandertreats.MainActivity;
import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.ProductActivity;
import com.wandertech.wandertreats.PurchasedDetailsActivity;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.adapter.FeedPostAdapter;
import com.wandertech.wandertreats.adapter.MainAdapter;
import com.wandertech.wandertreats.adapter.MyTreatsAdapter;
import com.wandertech.wandertreats.databinding.FragmentTreatsBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.FavoriteUtils;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.PopUpDialog;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.main.explore.ExploreFragment;
import com.wandertech.wandertreats.main.explore.ExploreViewModel;
import com.wandertech.wandertreats.model.ItemModel;
import com.wandertech.wandertreats.model.ParentModel;
import com.wandertech.wandertreats.utils.Utils;

import java.util.ArrayList;
import java.util.HashMap;

public class TreatsFragment  extends Fragment implements MyTreatsAdapter.ItemOnClickListener{

    private static String TYPE_ACTIVE = "ACTIVE";
    private static String TYPE_HISTORY = "HISTORY";
    private static String TYPE_FAVORITE = "FAVORITE";

    private ExploreViewModel exploreViewModel;
    private FragmentTreatsBinding binding;
    private TabLayout mainTab;
    private GeneralFunctions appFunctions;
    private TabItem activeTab, historyTab, favoriteTab;
    private String type = TYPE_ACTIVE;
    private RecyclerView treatsRecyclerList;
    private ShimmerFrameLayout loaderShimmer;
    private ArrayList<HashMap<String, String>> treatsArr = new ArrayList<>();
    private MyTreatsAdapter myTreatsAdapterAdapter;
    private LinearLayoutCompat treatsRecyclerListArea, noDataArea;
    private FavoriteUtils favoriteUtils;

    private ArrayList<ParentModel> mainArr = new ArrayList<>();
    private MainAdapter mainAdapter;

    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        appFunctions = MyApp.getInstance().getGeneralFun(container.getContext());
        favoriteUtils = new FavoriteUtils(getActContext(), appFunctions);

//        try {
//            ((MainActivity) getActivity()).  getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
//            appFunctions.setWindowFlag((Activity) getActivity(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
//            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
//                ((MainActivity) getActivity()).  getWindow().setStatusBarColor(getResources().getColor(R.color.gray,  ((MainActivity) getActivity()).getTheme()));
//            } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
//                ((MainActivity) getActivity()). getWindow().setStatusBarColor(getResources().getColor(R.color.gray));
//            }
//        }catch (Exception e){
//            appFunctions.showMessage(e.toString());
//        }

        binding = FragmentTreatsBinding.inflate(inflater, container, false);
        View root = binding.getRoot();

        if(getActivity() != null && isAdded()) {
            mainTab = binding.mainTab;
            loaderShimmer = binding.loaderShimmer;
            treatsRecyclerList= binding.treatsRecyclerList;
            noDataArea = binding.noDataArea;
            treatsRecyclerListArea = binding.treatsRecyclerListArea;

            //retriveData();

            mainTab.addOnTabSelectedListener(new TabLayout.OnTabSelectedListener() {
                @Override
                public void onTabSelected(TabLayout.Tab tab) {

                    if(tab.getPosition() == 0){
                        //appFunctions.showMessage("Active");
                        type = TYPE_ACTIVE;
                    }

                    if(tab.getPosition() == 1){
                        type = TYPE_HISTORY;
                        //  appFunctions.showMessage("History");

                    }

                    if(tab.getPosition() == 2){
                        type = TYPE_FAVORITE;

                        //appFunctions.showMessage(favoriteUtils.getFavoritemItemList());
                    }
                    retriveData();
                }

                @Override
                public void onTabUnselected(TabLayout.Tab tab) {

                }

                @Override
                public void onTabReselected(TabLayout.Tab tab) {

                }
            });

            retriveData();

        }



        return root;
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        binding = null;
    }


    public void retriveData() {

        loaderShimmer.setVisibility(View.VISIBLE);

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "RETRIEVE_DATA");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("favoriteIds", favoriteUtils.getFavoritemItemList());
        parameters.put("type", type);;

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActivity().getApplicationContext(), parameters, "api_load_purchased_products.php", true);
        exeWebServer.setLoaderConfig(getActivity().getApplicationContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){
                        loaderShimmer.setVisibility(View.GONE);

                        if(type.equalsIgnoreCase(TYPE_ACTIVE) || type.equalsIgnoreCase(TYPE_HISTORY)){
                            treatsArr  = Data.getMyPurchasedData(appFunctions.getJsonArray("data", responseString), appFunctions);
                            //Toast.makeText(getActContext(),feedsArr.toString(), Toast.LENGTH_SHORT).show();
                            if(treatsArr.size()>0){
                                loaderShimmer.setVisibility(View.GONE);
                                treatsRecyclerListArea.setVisibility(View.VISIBLE);
                                noDataArea.setVisibility(View.GONE);

                                myTreatsAdapterAdapter= new MyTreatsAdapter(getActContext(),  treatsArr);
                                treatsRecyclerList.setLayoutManager(new LinearLayoutManager(getActContext()));
                                treatsRecyclerList.setAdapter(myTreatsAdapterAdapter);
                                myTreatsAdapterAdapter.setOnItemClick(TreatsFragment.this::setOnItemClick);
                            }else{
                                loaderShimmer.setVisibility(View.GONE);
                                treatsRecyclerListArea.setVisibility(View.GONE);
                                noDataArea.setVisibility(View.VISIBLE);

                            }

                        }else if(type.equalsIgnoreCase(TYPE_FAVORITE)){

                            treatsArr.clear();

                            ArrayList<ItemModel> productArrayList = new ArrayList<>();
                            productArrayList = Data.getProductData(appFunctions.getJsonArray("favoriteData", responseString), appFunctions);

                            mainArr.clear();
                            treatsRecyclerList.clearOnChildAttachStateChangeListeners();
                            mainArr = Data.getParentData(appFunctions.getJsonArray("favoriteData", responseString), appFunctions);
                            appFunctions.showMessage(mainArr.size()+"");
                            mainAdapter = new MainAdapter(mainArr, getActivity().getApplicationContext(), appFunctions);
                            treatsRecyclerList.setLayoutManager(new LinearLayoutManager(getActivity().getApplicationContext(), LinearLayoutManager.VERTICAL, false));
                            treatsRecyclerList.setNestedScrollingEnabled(false);
                            treatsRecyclerList.setAdapter(mainAdapter);

                        }
                       // Toast.makeText(getActContext(), responseString, Toast.LENGTH_SHORT).show();
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

    public Context getActContext(){
        return   getActivity();
    }

    @Override
    public void setOnItemClick(int position) {

        try{
           // appFunctions.showMessage(treatsArr.get(position).get("data"));
            Bundle bn =  new Bundle();
            bn.putString("data",treatsArr.get(position).get("data"));
            new StartActProcess(getActivity()).startActWithData(PurchasedDetailsActivity.class,bn);
        }catch (Exception e){
            appFunctions.showMessage(e.toString());
        }


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



                default:

                    break;



            }

        }


    }
}