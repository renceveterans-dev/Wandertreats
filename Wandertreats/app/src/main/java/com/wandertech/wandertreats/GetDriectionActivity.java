package com.wandertech.wandertreats;

import android.Manifest;
import android.content.Context;
import android.content.pm.PackageManager;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.os.StrictMode;
import android.os.SystemClock;
import android.preference.PreferenceManager;
import android.view.View;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.cardview.widget.CardView;
import androidx.core.app.ActivityCompat;

import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.wandertech.wandertreats.databinding.ActivityGetDirectionBinding;
import com.wandertech.wandertreats.databinding.ActivityStoreBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import org.osmdroid.api.IMapController;
import org.osmdroid.config.Configuration;
import org.osmdroid.tileprovider.tilesource.TileSourceFactory;
import org.osmdroid.util.GeoPoint;
import org.osmdroid.views.MapView;
import org.osmdroid.views.overlay.ItemizedIconOverlay;
import org.osmdroid.views.overlay.OverlayItem;

import java.util.ArrayList;

public class GetDriectionActivity extends AppCompatActivity {

    private GeneralFunctions appFunctions;
    private ActivityGetDirectionBinding binding;

    private MapView map;
    private IMapController mapController;
    private ItemizedIconOverlay<OverlayItem> anotherItemizedIconOverlay;
    private ArrayList<OverlayItem> markerArray = new ArrayList<>();
    private GeoPoint storePointLocation;

    private String storeData = "";

    private MaterialToolbar toolbar;
    private FloatingActionButton getDirectionsBtn;
    private CardView currentLocationBtn;
    private AppCompatTextView titleTxt;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityGetDirectionBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        storeData = getIntent().getStringExtra("data");

        initMap();
        initView();

        setLabels();
        setListener();
    }

    private Context getActContext() {
        return GetDriectionActivity.this;
    }

    public void initView() {

        titleTxt = binding.mainToolbar.titleTxt;
        toolbar = binding.mainToolbar.toolbar;
        getDirectionsBtn = binding.getDirectionsBtn;
        currentLocationBtn = binding.currentLocationBtn;

    }

    public void setLabels() {

        titleTxt.setText(appFunctions.getJsonValue("vStoreName", storeData));
    }

    public void setListener() {

        getDirectionsBtn.setOnClickListener(new setOnClickAct());
        currentLocationBtn.setOnClickListener(new setOnClickAct());
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });


    }

    public void initMap() {

        //Initialize Maap
        Context ctx = getApplicationContext();
        Configuration.getInstance().load(ctx, PreferenceManager.getDefaultSharedPreferences(ctx));

        map = binding.map;
        map.setTileSource(TileSourceFactory.MAPNIK);
        map.setMinZoomLevel(6.0);
        map.setMaxZoomLevel(17.50);
        map.setMultiTouchControls(true);
        map.setBuiltInZoomControls(false);

        mapController = map.getController();
        mapController.setZoom(18f);
        storePointLocation = new GeoPoint(Double.parseDouble(appFunctions.getJsonValue("vLatitude", storeData)), Double.parseDouble(appFunctions.getJsonValue("vLongitude", storeData)));
        mapController.setCenter(storePointLocation);

        StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
        StrictMode.setThreadPolicy(policy);

        setMapFocus();

    }

    private void setMapFocus() {

        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION}, Utils.REQUEST_CODE_GPS_ON);
            return;
        }

        if(anotherItemizedIconOverlay != null && markerArray != null){

            map.getOverlays().remove(anotherItemizedIconOverlay);
            anotherItemizedIconOverlay.removeAllItems();
            markerArray.clear();

        }



        OverlayItem myLocation = new OverlayItem(appFunctions.getJsonValue("vStoreName", storeData), appFunctions.getJsonValue("vStoreName", storeData),
                storePointLocation);
        Drawable mylocationDrawable = new BitmapDrawable(getResources(), appFunctions.resizeMarkerUser(R.drawable.pin_location, 50, 50));
        myLocation.setMarker(mylocationDrawable);
        markerArray.add(myLocation);
        map.getController().animateTo(storePointLocation);

        anotherItemizedIconOverlay = new ItemizedIconOverlay<OverlayItem>(this, markerArray, null);
        map.getOverlays().add(anotherItemizedIconOverlay);

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

                case R.id.getDirectionsBtn:

                    try {
                        String url_view = "http://maps.google.com/maps?daddr=" + appFunctions.getJsonValue("vLatitude", storeData) + "," +  appFunctions.getJsonValue("vLongitude", storeData);
                        appFunctions.openURL(url_view, "com.google.android.apps.maps", "com.google.android.maps.MapsActivity");
                    } catch (Exception e) {
                        appFunctions.showMessage("Please install Google Maps in your device.");
                    }

                    break;

                case R.id.currentLocationBtn:
                    break;

            }

        }
    }
}
