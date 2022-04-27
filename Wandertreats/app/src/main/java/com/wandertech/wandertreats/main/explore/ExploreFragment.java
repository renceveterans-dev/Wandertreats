package com.wandertech.wandertreats.main.explore;

import android.Manifest;
import android.app.Activity;
import android.content.Context;
import android.content.pm.PackageManager;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.StrictMode;
import android.os.SystemClock;
import android.preference.PreferenceManager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.widget.AppCompatEditText;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.core.app.ActivityCompat;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProvider;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.LinearSnapHelper;
import androidx.recyclerview.widget.RecyclerView;

import com.facebook.shimmer.ShimmerFrameLayout;
import com.wandertech.wandertreats.MainActivity;
import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.SearchActivity;
import com.wandertech.wandertreats.StoreActivity;
import com.wandertech.wandertreats.adapter.MainItemAdapter;
import com.wandertech.wandertreats.adapter.MerchantAdapter;
import com.wandertech.wandertreats.adapter.MerchantItemAdapter;
import com.wandertech.wandertreats.databinding.FragmentExploreBinding;
import com.wandertech.wandertreats.general.CenterLayoutManager;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.model.ItemModel;
import com.wandertech.wandertreats.utils.Utils;

import org.osmdroid.api.IMapController;
import org.osmdroid.config.Configuration;
import org.osmdroid.tileprovider.tilesource.TileSourceFactory;
import org.osmdroid.util.GeoPoint;
import org.osmdroid.views.MapView;
import org.osmdroid.views.overlay.ItemizedIconOverlay;
import org.osmdroid.views.overlay.OverlayItem;

import java.util.ArrayList;
import java.util.HashMap;

public class ExploreFragment extends Fragment implements MerchantItemAdapter.ItemOnClickListener{

    private ExploreViewModel exploreViewModel;
    private FragmentExploreBinding binding;
    private MapView mapView;
    private RecyclerView mainRecyclerList;
    private AppCompatTextView searchTxt;
    private AppCompatImageView scanBtn;
    private ShimmerFrameLayout loaderShimmer;
    private GeneralFunctions appFunctions;
    private IMapController mapController;
    private GeoPoint storePointLocation;
    private ItemizedIconOverlay<OverlayItem> anotherItemizedIconOverlay;
    private ArrayList<OverlayItem> markerArray = new ArrayList<>();
    private String profileData = "";
    private long last_scroll = 0;
    private long delay = 1000;
    private long searchdelay = 500;
    private ArrayList<ItemModel> merchantArrayList = new ArrayList<>();
    private Drawable storelocationDrawable;

    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {



        appFunctions = MyApp.getInstance().getGeneralFun(container.getContext());
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

        binding = FragmentExploreBinding.inflate(inflater, container, false);
        View root = binding.getRoot();

        if(getActivity() != null && isAdded()) {

            try{
                profileData = appFunctions.retrieveValue(Utils.USER_PROFILE_JSON);

                mapView = binding.mapView;
                mainRecyclerList = binding.mainRecyclerList;
                searchTxt = binding.searchTxt;
                scanBtn = binding.scanBtn;
                loaderShimmer = binding.loaderShimmer;

                searchTxt.setOnClickListener(new setOnClickAct());

                initMap();

            }catch (Exception e){

            }

            loadData();


        }


        return root;
    }


    public void loadData() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_ALL_MERCHANTS");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("latitude", appFunctions.retrieveValue(Utils.CURRENT_LATITUDE));
        parameters.put("longitude", appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE));
        parameters.put("longitude", appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE));
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_merchants_list.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        try{



                            merchantArrayList = Data.getMerchantAllListData(appFunctions.getJsonArray("data", responseString), appFunctions);
                            setStoreLocationMarker(false, 0);
                            //appFunctions.showMessage(appFunctions.getJsonArray("productData", storeData).toString());

                            MerchantItemAdapter MerchantItemAdapter = new MerchantItemAdapter(merchantArrayList, getActivity());
                            CenterLayoutManager layoutManager = new CenterLayoutManager(getActivity(), LinearLayoutManager.HORIZONTAL, false);
                            mainRecyclerList.setLayoutManager(layoutManager);
                            mainRecyclerList.setAdapter(MerchantItemAdapter);
                            MerchantItemAdapter.setOnItemClick(ExploreFragment.this::setOnItemClick);
                            // Scroll to the position we want to snap to
                            layoutManager.scrollToPosition(merchantArrayList.size() / 2);
                            // Wait until the RecyclerView is laid out.
                            mainRecyclerList.post(new Runnable() {
                                @Override
                                public void run() {
                                    // Shift the view to snap  near the center of the screen.
                                    // This does not have to be precise.
                                    int dx = (  mainRecyclerList.getWidth() -  mainRecyclerList.getChildAt(0).getWidth()) / 2;
                                    mainRecyclerList.scrollBy(-dx, 0);
                                    // Assign the LinearSnapHelper that will initially snap the near-center view.
                                    LinearSnapHelper snapHelper = new LinearSnapHelper() {
                                        @Override
                                        public int findTargetSnapPosition(RecyclerView.LayoutManager layoutManager, int velocityX, int velocityY) {
                                            View centerView = findSnapView(layoutManager);
                                            if (centerView == null)
                                                return RecyclerView.NO_POSITION;

                                            int position = layoutManager.getPosition(centerView);
                                            int targetPosition = -1;
                                            if (layoutManager.canScrollHorizontally()) {
                                                if (velocityX < 0) {
                                                    targetPosition = position - 1;
                                                } else {
                                                    targetPosition = position + 1;
                                                }
                                            }

                                            if (layoutManager.canScrollVertically()) {
                                                if (velocityY < 0) {
                                                    targetPosition = position - 1;
                                                } else {
                                                    targetPosition = position + 1;
                                                }
                                            }

                                            final int firstItem = 0;
                                            final int lastItem = layoutManager.getItemCount() - 1;
                                            targetPosition = Math.min(lastItem, Math.max(targetPosition, firstItem));
                                            return targetPosition;
                                        }
                                    };
                                    snapHelper.attachToRecyclerView( mainRecyclerList);
                                }
                            });
                            mainRecyclerList.setVisibility(View.VISIBLE);
                            loaderShimmer.setVisibility(View.GONE);

                            ArrayList<ItemModel> finalMerchantArrayList = merchantArrayList;
                            mainRecyclerList.addOnScrollListener(new RecyclerView.OnScrollListener() {
                                @Override
                                public void onScrollStateChanged(@NonNull RecyclerView recyclerView, int newState) {
                                    super.onScrollStateChanged(recyclerView, newState);
                                    int review_position = layoutManager.findFirstVisibleItemPosition();



                                    last_scroll = System.currentTimeMillis();
                                    new Handler().postDelayed(new Runnable() {
                                        @Override
                                        public void run() {
                                            //Do something after 100ms

                                            try {

                                                if (System.currentTimeMillis() > (last_scroll + delay - 500)) {
                                                    setStoreLocationMarker(true, review_position);
                                                    GeoPoint centerPoint = new GeoPoint(Double.parseDouble(appFunctions.getJsonValue("vLatitude", finalMerchantArrayList.get(review_position).getData())), Double.parseDouble(appFunctions.getJsonValue("vLongitude", finalMerchantArrayList.get(review_position).getData())));
                                                    mapView.getController().animateTo(centerPoint);
                                                    //appFunctions.showMessage(finalMerchantArrayList.get(review_position).getTitle()+"");

                                                }

                                            }catch (Exception e){

                                            }

                                        }
                                    }, delay);


                                }
                            });

                            setStoreLocationMarker(true, 0);


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


    public void setStoreLocationMarker(boolean isSelect, int position){
        if(anotherItemizedIconOverlay != null && markerArray != null){

            mapView.getOverlays().remove(anotherItemizedIconOverlay);
            anotherItemizedIconOverlay.removeAllItems();
            markerArray.clear();

        }

        for(int x = 0; x < merchantArrayList.size(); x++){

            if(isSelect){
                if(position == x){
                    ItemModel item = merchantArrayList.get(x);
                    GeoPoint location = new GeoPoint(Double.parseDouble(appFunctions.getJsonValue("vLatitude", item.getData())), Double.parseDouble(appFunctions.getJsonValue("vLongitude", item.getData())));
                    OverlayItem driverLocation = new OverlayItem(item.getTitle(), item.getTitle(), location);
                    storelocationDrawable = new BitmapDrawable(getResources(), appFunctions.resizeMarkerUser(R.drawable.pin_location, 60, 60));
                    driverLocation.setMarker(storelocationDrawable);
                    driverLocation.setMarkerHotspot(OverlayItem.HotspotPlace.CENTER);
                    markerArray.add(driverLocation);
                }else{
                    ItemModel item = merchantArrayList.get(x);
                    GeoPoint location = new GeoPoint(Double.parseDouble(appFunctions.getJsonValue("vLatitude", item.getData())), Double.parseDouble(appFunctions.getJsonValue("vLongitude", item.getData())));
                    OverlayItem driverLocation = new OverlayItem(item.getTitle(), item.getTitle(), location);
                    storelocationDrawable = new BitmapDrawable(getResources(), appFunctions.resizeMarkerUser(R.drawable.pin_location, 30, 30));
                    driverLocation.setMarker(storelocationDrawable);
                    driverLocation.setMarkerHotspot(OverlayItem.HotspotPlace.CENTER);
                    markerArray.add(driverLocation);
                }

            }else{
                ItemModel item = merchantArrayList.get(x);
                GeoPoint location = new GeoPoint(Double.parseDouble(appFunctions.getJsonValue("vLatitude", item.getData())), Double.parseDouble(appFunctions.getJsonValue("vLongitude", item.getData())));
                OverlayItem driverLocation = new OverlayItem(item.getTitle(), item.getTitle(), location);
                storelocationDrawable = new BitmapDrawable(getResources(), appFunctions.resizeMarkerUser(R.drawable.pin_location, 30, 53));
                driverLocation.setMarker(storelocationDrawable);
                driverLocation.setMarkerHotspot(OverlayItem.HotspotPlace.CENTER);
                markerArray.add(driverLocation);
            }

        }

        anotherItemizedIconOverlay = new ItemizedIconOverlay<OverlayItem>(getActContext(), markerArray, null);
        mapView.getOverlays().add(anotherItemizedIconOverlay);
    }




    public void initMap() {
        Context ctx = getActContext();
        Configuration.getInstance().load(ctx, PreferenceManager.getDefaultSharedPreferences(ctx));

        mapView = binding.mapView;
        mapView.setTileSource(TileSourceFactory.MAPNIK);
        mapView.setMinZoomLevel(6.0);
        mapView.setMaxZoomLevel(18.50);
        mapView.setMultiTouchControls(true);
        mapView.setBuiltInZoomControls(false);

        mapController = mapView.getController();
        mapController.setZoom(18f);
        storePointLocation = new GeoPoint(Double.parseDouble(appFunctions.getJsonValue("vLatitude", profileData)), Double.parseDouble(appFunctions.getJsonValue("vLongitude", profileData)));
        mapController.setCenter(storePointLocation);

        StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
        StrictMode.setThreadPolicy(policy);

        //setMapFocus();

    }


    private void setMapFocus() {

        if (ActivityCompat.checkSelfPermission(getActContext(), Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(getActContext(), Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(getActivity(), new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION}, Utils.REQUEST_CODE_GPS_ON);
            return;
        }

        if(anotherItemizedIconOverlay != null && markerArray != null){

            mapView.getOverlays().remove(anotherItemizedIconOverlay);
            anotherItemizedIconOverlay.removeAllItems();
            markerArray.clear();

        }



        OverlayItem myLocation = new OverlayItem(appFunctions.getJsonValue("vName", profileData), appFunctions.getJsonValue("vName", profileData),
                storePointLocation);
        Drawable mylocationDrawable = new BitmapDrawable(getResources(), appFunctions.resizeMarkerUser(R.drawable.pin_location, 50, 50));
        myLocation.setMarker(mylocationDrawable);
        markerArray.add(myLocation);
        mapView.getController().animateTo(storePointLocation);

        anotherItemizedIconOverlay = new ItemizedIconOverlay<OverlayItem>(getActContext(), markerArray, null);
        mapView.getOverlays().add(anotherItemizedIconOverlay);

    }

    public Context getActContext(){
        return  getActivity().getApplicationContext();
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        binding = null;
    }

    @Override
    public void setOnItemClick(int position) {

       // appFunctions.showMessage(merchantArrayList.get(position).getData());

        try{
            Bundle bn =  new Bundle();
            bn.putString("data",merchantArrayList.get(position).getData());
            new StartActProcess(getActivity()).startActWithData(StoreActivity.class,bn);
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

                case R.id.searchTxt:
                    new StartActProcess(getActivity()).startAct(SearchActivity.class);
                    break;

                default:

                    break;



            }

        }


    }
}