package com.wandertech.wandertreats;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Notification;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.graphics.drawable.ColorDrawable;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.view.LayoutInflater;
import android.view.View;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.material.bottomnavigation.BottomNavigationItemView;
import com.google.android.material.bottomnavigation.BottomNavigationMenuView;
import com.google.android.material.bottomnavigation.BottomNavigationView;
import com.google.android.material.badge.BadgeDrawable;
import com.google.android.material.bottomnavigation.BottomNavigationView;

import androidx.annotation.IdRes;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.navigation.NavController;
import androidx.navigation.NavOptions;
import androidx.navigation.Navigation;
import androidx.navigation.fragment.NavHostFragment;
import androidx.navigation.ui.AppBarConfiguration;
import androidx.navigation.ui.NavigationUI;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import okhttp3.internal.Util;

import com.google.android.material.button.MaterialButton;
import com.wandertech.wandertreats.adapter.GridCategoryAdapter;
import com.wandertech.wandertreats.adapter.MainAdapter;
import com.wandertech.wandertreats.databinding.ActivityMainBinding;
import com.wandertech.wandertreats.general.BackgroundLocationUpdateService;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.GetAddressFromLocation;
import com.wandertech.wandertreats.general.LocationService;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.main.home.HomeFragment;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;

import static com.wandertech.wandertreats.utils.Constants.NOTIFICATION_ID;

public class MainActivity extends BaseActivity implements GetAddressFromLocation.AddressFound {

    private ActivityMainBinding binding;
    public BottomNavigationView navView;
    private GeneralFunctions appFunctions;
    private GetAddressFromLocation  getAddressFromLocation;
    private LocationListener mLocationListener;
    private String isStart = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityMainBinding.inflate(getLayoutInflater());
        try{
            if( getIntent().getStringExtra("isStart") != null){
                isStart = getIntent().getStringExtra("isStart");
            }
        }catch (Exception e){

        }

        setContentView(binding.getRoot());
        getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);

        appFunctions.setWindowFlag((Activity) getActContext(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent));
        }


        getAddressFromLocation = new GetAddressFromLocation(getActContext(), appFunctions);
        getAddressFromLocation.setAddressList(this);
        getAddressFromLocation.setLocation(Double.parseDouble(appFunctions.retrieveValue(Utils.CURRENT_LATITUDE)), Double.parseDouble(appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE)));
        getAddressFromLocation.execute();


       //navView = binding.navView;
        // Passing each menu ID as a set of Ids because each
        // menu should be considered as top level destinations.
        AppBarConfiguration appBarConfiguration = new AppBarConfiguration.Builder(
                R.id.navigation_home, R.id.navigation_explore, R.id.navigation_feed, R.id.navigation_treats, R.id.navigation_account)
                .build();


        NavController navController = Navigation.findNavController(this, R.id.nav_host_fragment_activity_main);

      //  NavigationUI.setupActionBarWithNavController(this, navController, appBarConfiguration);
        NavigationUI.setupWithNavController(binding.navView, navController);

        init();

       // startService(new Intent(this, LocationService.class));
        if(isStart.equalsIgnoreCase("Explore")){
            onNavDestinationSelected(R.id.navigation_explore, navController);
        }else if(isStart.equalsIgnoreCase("Feeds")){
            onNavDestinationSelected(R.id.navigation_feed, navController);
        }else if(isStart.equalsIgnoreCase("Treats")){
            onNavDestinationSelected(R.id.navigation_treats, navController);
        }else if(isStart.equalsIgnoreCase("Account")){
            onNavDestinationSelected(R.id.navigation_account, navController);
        }else{
            onNavDestinationSelected(R.id.navigation_home, navController);
        }
    }

    private static void onNavDestinationSelected(int id, @NonNull NavController navController) {
        NavOptions options = new NavOptions.Builder()
                .setLaunchSingleTop(true)
                .setEnterAnim(R.anim.nav_default_enter_anim)
                .setExitAnim(R.anim.nav_default_exit_anim)
                .setPopEnterAnim(R.anim.nav_default_pop_enter_anim)
                .setPopExitAnim(R.anim.nav_default_pop_exit_anim)
                .setPopUpTo(navController.getGraph().getStartDestination(), false)
                .build();
        try {
            navController.navigate(id, null, options);
        } catch (IllegalArgumentException e) {
           // e.printStackTrace();
        }
    }


    public void init(){

        String isForceUpdate = appFunctions.getJsonValue("APP_FORCE_UPDATE", appFunctions.retrieveValue(Utils.APP_GENERAL_DATA));
        if(isForceUpdate.equalsIgnoreCase("Yes")){
            new Handler().postDelayed(new Runnable() {

                @Override
                public void run() {

                    AlertDialog.Builder builder = new AlertDialog.Builder(getActContext());
                    LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService( Context.LAYOUT_INFLATER_SERVICE );
                    View dialog = inflater.inflate( R.layout.dialog_alert_3, null );

                    AppCompatTextView title = dialog.findViewById(R.id.title);
                    AppCompatTextView message = dialog.findViewById( R.id.message );
                    MaterialButton positive_btn = dialog.findViewById( R.id.positive_btn);
                    MaterialButton negative_btn = dialog.findViewById( R.id.negative_btn);
                    ImageView alertIcon = dialog.findViewById(R.id.alertIcon);
                    builder.setView(dialog);

                    title.setText("Theres a shiny new update!");
                    message.setText("We have a new version of "+getString(R.string.app_name) + " available. Update now and keep enjoying.");
                    positive_btn.setText("UPDATE");
                    negative_btn.setText("LATER");
                    alertIcon.setBackgroundResource(R.drawable.system_update);

                    AlertDialog alert = builder.create();
                    alert.setCancelable(false);
                    alert.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
                    alert.show();

                    positive_btn.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View view) {

                            alert.dismiss();
                            final String appPackageName = getPackageName(); // getPackageName() from Context or Activity object
                            try {
                                startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=" + appPackageName)));
                            } catch (android.content.ActivityNotFoundException anfe) {
                                startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + appPackageName)));
                            }
                        }
                    });
                    negative_btn.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View view) {

                            alert.dismiss();

                        }
                    });
                }
            }, 2000);
        }

    }

    public void loadBadges() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_DATA");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("latitude", appFunctions.retrieveValue(Utils.CURRENT_LATITUDE));
        parameters.put("longitude", appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE));
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_notification_counter.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        try{

                            int treatsCounter = Integer.parseInt(appFunctions.getJsonValue("treatsCounter", responseString));
                            int feedsCounter = Integer.parseInt(appFunctions.getJsonValue("feedsCounter", responseString));
                            int accountCounter = Integer.parseInt(appFunctions.getJsonValue("accountCounter", responseString));

                            setBadgeCounter(R.id.navigation_treats, treatsCounter);
                            setBadgeCounter(R.id.navigation_feed, feedsCounter);
                            setBadgeCounter(R.id.navigation_account, accountCounter);

                        }catch (Exception e){
                            appFunctions.showMessage("hahaha"+e.toString());
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

    public void setBadgeCounter(int menuItemId, int count){
        BottomNavigationView bottomNav  = findViewById(R.id.nav_view);
        if(count > 0){
            bottomNav.getOrCreateBadge(menuItemId).setBackgroundColor(getActContext().getResources().getColor(R.color.appThemeColor_warning));
            bottomNav.getOrCreateBadge(menuItemId).setNumber(count);
            bottomNav.getOrCreateBadge(menuItemId).setVisible(true);
        }else{
            bottomNav.getOrCreateBadge(menuItemId).clearNumber();
            bottomNav.getOrCreateBadge(menuItemId).setVisible(false);
        }
    }

    public Context getActContext(){
        return MainActivity.this;
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadBadges();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        switch(requestCode){
            case Constants.LOCATION_PICKER:

                String address = data.getStringExtra("address");
                String latitude = data.getStringExtra("lat");
                String longitude = data.getStringExtra("long");

                if(!address.equalsIgnoreCase("") && !latitude.equalsIgnoreCase("") && !longitude.equalsIgnoreCase("")){
                    if(getLocationListener()  != null){
                        getLocationListener().onLocationFound(address, Double.parseDouble(latitude), Double.parseDouble(longitude));
                    }
                }



               //appFunctions.showMessage("Location Picker Result");

                break;

            default:
                break;

        }
    }

    @Override
    public void onAddressFound(String address, double latitude, double longitude, String geocodeobject) {
        if(getLocationListener()  != null){
            getLocationListener().onLocationFound(address, latitude, longitude);
        }
    }

    public LocationListener getLocationListener() {
        return mLocationListener;
    }

    public void setLocationListener(LocationListener LocationListener) {
        this.mLocationListener = LocationListener;
    }


    public interface LocationListener{
        void onLocationFound(String address, double latitude, double longitude);
    }
}