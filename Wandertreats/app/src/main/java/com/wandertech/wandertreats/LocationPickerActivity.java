package com.wandertech.wandertreats;

import android.Manifest;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Location;
import android.os.Bundle;
import android.os.Handler;
import android.os.StrictMode;
import android.os.SystemClock;
import android.preference.PreferenceManager;
import android.view.View;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationCallback;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationResult;
import com.google.android.gms.location.LocationServices;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;
import com.wandertech.wandertreats.databinding.ActivityLocationPickerBinding;
import com.wandertech.wandertreats.databinding.ActivityStoreBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.GetAddressFromLocation;
import com.wandertech.wandertreats.general.GpsUtils;
import com.wandertech.wandertreats.general.MyBounceInterpolator;
import com.wandertech.wandertreats.general.ProgressDialog;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import org.osmdroid.api.IGeoPoint;
import org.osmdroid.api.IMapController;
import org.osmdroid.config.Configuration;
import org.osmdroid.events.DelayedMapListener;
import org.osmdroid.events.MapListener;
import org.osmdroid.events.ScrollEvent;
import org.osmdroid.events.ZoomEvent;
import org.osmdroid.tileprovider.tilesource.TileSourceFactory;
import org.osmdroid.util.GeoPoint;
import org.osmdroid.views.MapView;

import java.util.Timer;

import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.cardview.widget.CardView;
import androidx.core.app.ActivityCompat;
import androidx.core.widget.ImageViewCompat;

public class LocationPickerActivity extends AppCompatActivity implements GetAddressFromLocation.AddressFound {

    private ActivityLocationPickerBinding binding;
    private GeneralFunctions appFunctions;
    private long delay = 1500;
    private long searchdelay = 500;
    private long last_move = 0;
    private long last_search = 0;
    private Timer timer;
    private Handler latlonghandler = new Handler();
    private Handler searchhandler = new Handler();
    private FusedLocationProviderClient mFusedLocationClient;
    private MaterialButton ConfirmBtn;
    private double wayLatitude = 0.0, wayLongitude = 0.0;
    private LocationRequest locationRequest;
    private LocationCallback locationCallback;
    private String latitude = "", longitude = "", addressFound = "", provider;
    private AppCompatImageView backImgView;
//    private Location currentLocation;
    private GeoPoint currentLocation;
    private boolean isGPS = false;
    private static final int REQUEST_CODE = 101;
    private double mlatitude = 0.0, mlongitude = 0.0;
    private MapView map;
    private IMapController mapController;
    private AppCompatTextView titleTxt;
    private ImageView pinlocation;
    private LinearLayout searchArea;
    private AppCompatButton confirmBtn;
    private AppCompatTextView searchTextView;
    private LinearLayout searchCardArea;
    private GetAddressFromLocation  getAddressFromLocation;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityLocationPickerBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        map = binding.map;
        titleTxt = binding.header.titleTxt;
        pinlocation = binding.pinlocation;
        searchArea = binding.searchArea;
        searchCardArea = binding.searchCardArea;
        confirmBtn = binding.confirmBtn;
        searchTextView = binding.searchTextView;
        backImgView = binding.header.backImgView;

        titleTxt.setText("Set Location");

        backImgView.setOnClickListener(new setOnClickAct());
        searchArea.setOnClickListener(new setOnClickAct());
        searchCardArea.setOnClickListener(new setOnClickAct());
        confirmBtn.setOnClickListener(new setOnClickAct());

        getAddressFromLocation = new GetAddressFromLocation(getActContext(), appFunctions);
        getAddressFromLocation.setAddressList(this);

        initLocation();
        initMap();
    }

    public void initMap(){
        Context ctx = getApplicationContext();
        Configuration.getInstance().load(ctx, PreferenceManager.getDefaultSharedPreferences(ctx));

        map = (MapView) findViewById(R.id.map);
        map.setTileSource(TileSourceFactory.MAPNIK);
        map.setMinZoomLevel(6.0);
        map.setMaxZoomLevel(17.50);
        map.setMultiTouchControls(true);
        map.setBuiltInZoomControls(false);

        mapController = map.getController();
        mapController.setZoom(18f);
        GeoPoint startPoint = new GeoPoint(14.6047068, 121.053133);
        mapController.setCenter(startPoint);

        StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
        StrictMode.setThreadPolicy(policy);


        map.addMapListener(new DelayedMapListener(new MapListener() {
            @Override
            public boolean onScroll(ScrollEvent event) {
                IGeoPoint mapCenter = map.getMapCenter();
//
                latitude = String.format("%.5f", mapCenter.getLatitude());
                longitude = String.format("%.5f", mapCenter.getLongitude());

                try {
                    currentLocation = new GeoPoint(Double.parseDouble(latitude), Double.parseDouble(longitude));
                }catch (Exception e){
                    appFunctions.showMessage(e.toString());
                }

                appFunctions.doBounceAnimation(pinlocation);

                return false;
            }

            @Override
            public boolean onZoom(ZoomEvent event) {
                return false;
            }
        }, Constants.DEFAULT_INACTIVITY_DELAY_IN_MILLISECS));
    }



    public void initLocation(){
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION}, Constants.GPS_REQUEST);
            return;
        }

        mFusedLocationClient = LocationServices.getFusedLocationProviderClient(this);

        locationRequest = LocationRequest.create();
        locationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
        locationRequest.setInterval(10 * 1000); // 10 seconds
        locationRequest.setFastestInterval(2 * 1000); // 5 seconds

        new GpsUtils(this).turnGPSOn(new GpsUtils.onGpsListener() {
            @Override
            public void gpsStatus(boolean isGPSEnable) {
                isGPS = isGPSEnable;
            }
        });

        locationCallback = new LocationCallback() {
            @Override
            public void onLocationResult(LocationResult locationResult) {

                try {
                    if (locationResult == null) {
                        return;
                    }
                    for (Location location : locationResult.getLocations()) {
                        if (location != null) {
                            mlatitude = location.getLatitude();
                            mlatitude = location.getLongitude();
                            currentLocation.setLatitude(mlatitude);
                            currentLocation.setLongitude(mlongitude);

                            if (mFusedLocationClient != null) {
                                mFusedLocationClient.removeLocationUpdates(locationCallback);
                            }
                        }
                    }

                }catch (Exception e){

                }
            }
        };
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        switch(requestCode){
            case Constants.SEARCH_LOCATION:

                String address = data.getStringExtra("address");
                String lat = data.getStringExtra("lat");
                String lng = data.getStringExtra("long");

                if(!address.equalsIgnoreCase("")){
                    searchTextView.setText(address);
                    currentLocation.setLatitude(Double.parseDouble(lat));
                    currentLocation.setLongitude(Double.parseDouble(lng));
                    map.getController().animateTo(currentLocation);
                }

                break;

            default:
                break;

        }

    }

    @Override
    public void onAddressFound(String address, double latitude, double longitude, String geocodeobject) {

        appFunctions.storeData(Utils.CURRENT_ADDRESSS, address);
        appFunctions.storeData(Utils.CURRENT_LATITUDE, latitude+ "");
        appFunctions.storeData(Utils.CURRENT_LONGITUDE, longitude + "");

        Bundle bn = new Bundle();
        bn.putString("address", address);
        bn.putString("lat", latitude+"");
        bn.putString("long", longitude+"");

        new StartActProcess(getActContext()).setOkResult(bn);
        finish();

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

                case R.id. backImgView:
                    onBackPressed();
                    break;

                case R.id.searchArea:
                case R.id.searchCardArea:

                    new StartActProcess(getActContext()).startActForResult(SearchLocationActivity.class, bn, Constants.SEARCH_LOCATION);
                    break;

                case R.id.confirmBtn:


                    getAddressFromLocation.setLocation(currentLocation.getLatitude(), currentLocation.getLongitude());
                    getAddressFromLocation.setLoaderEnable(true);
                    getAddressFromLocation.execute();

                    break;

                default:
                    break;

            }

        }

    }

    @Override
    public void onBackPressed() {

        Bundle bn = new Bundle();
        bn.putString("address","");
        bn.putString("lat", "");
        bn.putString("long", "");

        new StartActProcess(getActContext()).setOkResult(bn);
        finish();
    }

    private Context getActContext() {
        return LocationPickerActivity.this;
    }
}
