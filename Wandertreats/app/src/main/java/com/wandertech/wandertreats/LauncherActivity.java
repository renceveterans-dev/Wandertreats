package com.wandertech.wandertreats;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.pm.PackageManager;
import android.location.Location;
import android.location.LocationManager;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.view.View;
import android.view.WindowInsets;
import android.view.WindowManager;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import okhttp3.internal.Util;

import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationCallback;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationResult;
import com.google.android.gms.location.LocationServices;
import com.wandertech.wandertreats.databinding.ActivityLauncherBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.GpsUtils;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;

@SuppressLint("MissingPermission")
public class LauncherActivity extends AppCompatActivity {

    private ActivityLauncherBinding binding;
    private View contentView;
    private final Handler handler = new Handler();
    private GeneralFunctions appFunctions;
    private LocationManager locationManager;

    private FusedLocationProviderClient mFusedLocationClient;
    private double wayLatitude = 0.0, wayLongitude = 0.0;
    private LocationRequest locationRequest;
    private LocationCallback locationCallback;
    private boolean isGPS = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityLauncherBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());
        contentView = binding.contentView;

        getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);

        if (Build.VERSION.SDK_INT >= 30) {
            contentView.getWindowInsetsController().hide( WindowInsets.Type.navigationBars());
            //appFunctions.showMessage("yes");
        } else {
           // appFunctions.showMessage("no");
            // Note that some of these constants are new as of API 16 (Jelly Bean)
            // and API 19 (KitKat). It is safe to use them, as they are inlined
            // at compile-time and do nothing on earlier devices.
            contentView.setSystemUiVisibility(View.SYSTEM_UI_FLAG_LOW_PROFILE
                    | View.SYSTEM_UI_FLAG_FULLSCREEN
                    | View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                    | View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY
                    | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                    | View.SYSTEM_UI_FLAG_HIDE_NAVIGATION);
        }

        appFunctions.setWindowFlag((Activity) getActContext(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent));
        }

        if (appFunctions.isFirstTimeLaunch()) {
            appFunctions.initDedaultData();
        }

        initLocation();
        loadGeneralData();


    }

    public void initLocation() {

        try {
            if (ContextCompat.checkSelfPermission(LauncherActivity.this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED &&
                    ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
                ActivityCompat.requestPermissions(LauncherActivity.this, new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION}, 1);
                return;
            }

            mFusedLocationClient = LocationServices.getFusedLocationProviderClient(this);

            locationRequest = LocationRequest.create();
            locationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
            locationRequest.setInterval(5 * 1000); // 10 seconds
            locationRequest.setFastestInterval(5 * 1000); // 5 seconds


            new GpsUtils(this).turnGPSOn(new GpsUtils.onGpsListener() {
                @Override
                public void gpsStatus(boolean isGPSEnable) {
                    isGPS = isGPSEnable;
                }
            });


            locationCallback = new LocationCallback() {
                @Override
                public void onLocationResult(LocationResult locationResult) {
                    if (locationResult == null) {
                        return;
                    }
                    for (Location location : locationResult.getLocations()) {
                        if (location != null) {
                            appFunctions.storeData(Utils.CURRENT_LATITUDE, location.getLatitude() + "");
                            appFunctions.storeData(Utils.CURRENT_LONGITUDE, location.getLongitude() + "");
                            //appFunctions.showMessage(appFunctions.retrieveValue(Utils.CURRENT_LATITUDE)+" "+appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE));
                            if (mFusedLocationClient != null) {
                                mFusedLocationClient.removeLocationUpdates(locationCallback);
                            }
                        }
                    }
                }
            };


            mFusedLocationClient.requestLocationUpdates(locationRequest,
                    locationCallback,
                    Looper.getMainLooper());

        } catch (Exception e) {

        }

    }



    public void loadGeneralData() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_GENERAL_DATA");
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_general_data.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        try{

                            appFunctions.storeData(Utils.APP_GENERAL_DATA, appFunctions.getJsonValue("data", responseString));
                            handler.postDelayed(new Runnable() {

                                @Override
                                public void run() {

                                    if (appFunctions.isUserLoggedIn()) {
                                        new StartActProcess(getActContext()).startAct(MainActivity.class);
                                        finish();
                                    } else {
                                        new StartActProcess(getActContext()).startAct(WelcomeActivity.class);
                                        finish();
                                    }
                                }
                            }, 1000);

                        }catch (Exception d){
                            appFunctions.showMessage(exeWebServer.toString());
                        }

                    }else{


                    }

                }

                //
            }
        });
        exeWebServer.execute();
    }


    public Context getActContext() {
            return LauncherActivity.this;
        }
}

