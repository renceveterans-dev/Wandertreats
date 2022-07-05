package com.wandertech.wandertreats.store;;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Notification;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.material.appbar.MaterialToolbar;
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
import androidx.navigation.NavDestination;
import androidx.navigation.NavOptions;
import androidx.navigation.Navigation;
import androidx.navigation.fragment.NavHostFragment;
import androidx.navigation.ui.AppBarConfiguration;
import androidx.navigation.ui.NavigationUI;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import okhttp3.internal.Util;

import com.google.android.material.button.MaterialButton;
import com.wandertech.wandertreats.store.adapter.GridCategoryAdapter;
import com.wandertech.wandertreats.store.adapter.MainAdapter;
import com.wandertech.wandertreats.store.databinding.ActivityMainBinding;
import com.wandertech.wandertreats.store.general.BackgroundLocationUpdateService;
import com.wandertech.wandertreats.store.general.Data;
import com.wandertech.wandertreats.store.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.GetAddressFromLocation;
import com.wandertech.wandertreats.store.general.LocationService;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.main.home.HomeFragment;
import com.wandertech.wandertreats.store.utils.Constants;
import com.wandertech.wandertreats.store.utils.Utils;

import java.util.HashMap;

import static com.wandertech.wandertreats.store.utils.Constants.NOTIFICATION_ID;

public class MainActivity extends BaseActivity implements GetAddressFromLocation.AddressFound {

    private ActivityMainBinding binding;
    public BottomNavigationView navView;
    private GeneralFunctions appFunctions;
    private GetAddressFromLocation  getAddressFromLocation;
    private LocationListener mLocationListener;
    private String isStart = "";
    private MaterialToolbar toolbar;
    private int currentTab;

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
        //transparentStatusAndNavigation();

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

        AppBarConfiguration appBarConfiguration = new AppBarConfiguration.Builder(
                R.id.navigation_home, R.id.navigation_explore, R.id.navigation_feed, R.id.navigation_treats, R.id.navigation_account)
                .build();
        NavController navController = Navigation.findNavController(this, R.id.nav_host_fragment_activity_main);
      //  NavigationUI.setupActionBarWithNavController(this, navController, appBarConfiguration);
        NavigationUI.setupWithNavController(binding.navView, navController);




        binding.navView.setOnNavigationItemSelectedListener(new BottomNavigationView.OnNavigationItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem item) {
               // appFunctions.showMessage(item.getTitle()+" | "+ item.getItemId());
                if(currentTab == item.getItemId()){
                    return false;
                }else{
                    if(item.getItemId() == R.id.navigation_explore) {
//                        currentTab = R.id.navigation_explore;
                        onNavDestinationSelected(R.id.navigation_explore, navController);
                    }else if(item.getItemId() == R.id.navigation_feed) {
                        currentTab = R.id.navigation_feed;
                        onNavDestinationSelected( R.id.navigation_feed, navController);
                    }else if(item.getItemId() == R.id.navigation_treats) {
                        currentTab = R.id.navigation_treats;
                        onNavDestinationSelected(R.id.navigation_treats, navController);
                    }else if(item.getItemId() == R.id.navigation_account) {
                        currentTab = R.id.navigation_account;
                        onNavDestinationSelected( R.id.navigation_account, navController);
                    }else if(item.getItemId() == R.id.navigation_home) {
                        currentTab = R.id.navigation_home;
                        onNavDestinationSelected(R.id.navigation_home, navController);
                        // appFunctions.showMessage("home");
                    }
                    return true;
                }


            }
        });

        if(!appFunctions.isEmailVerified()){
            new StartActProcess(getActContext()).startAct(VerifyActivity.class);
        }

        navController.addOnDestinationChangedListener(new NavController.OnDestinationChangedListener() {
            @Override
            public void onDestinationChanged(@NonNull NavController controller, @NonNull NavDestination destination, @Nullable Bundle arguments) {
               // appFunctions.showMessage((controller.getCurrentDestination().getId()+" | "+destination.getId()));
                if(controller.getCurrentDestination().getId() == destination.getId()){
                    if(destination.getId() == R.id.navigation_explore) {
                        currentTab = R.id.navigation_explore;
                        //onNavDestinationSelected(R.id.navigation_explore, navController);
                    }else if(destination.getId() == R.id.navigation_feed) {
                        currentTab = R.id.navigation_feed;
                        //onNavDestinationSelected( R.id.navigation_feed, navController);
                    }else if(destination.getId() == R.id.navigation_treats) {
                        currentTab = R.id.navigation_treats;
                        //onNavDestinationSelected(R.id.navigation_treats, navController);
                    }else if(destination.getId() == R.id.navigation_account) {
                        currentTab = R.id.navigation_account;
                        //onNavDestinationSelected(R.id.navigation_home, navController);
                    }else if(destination.getId() == R.id.navigation_home) {
                        currentTab = R.id.navigation_home;
                        //onNavDestinationSelected(R.id.navigation_home, navController);
                       // appFunctions.showMessage("home");
                    }
                }


            }
        });

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

    @Override
    protected void onSaveInstanceState(@NonNull Bundle outState) {
        super.onSaveInstanceState(outState);
    }

    private void transparentStatusAndNavigation() {
        //make full transparent statusBar
        if (Build.VERSION.SDK_INT >= 19 && Build.VERSION.SDK_INT < 21) {
            setWindowFlag(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS
                    | WindowManager.LayoutParams.FLAG_TRANSLUCENT_NAVIGATION, true);
        }
        if (Build.VERSION.SDK_INT >= 19) {
            getWindow().getDecorView().setSystemUiVisibility(
                    View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                            | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
                            | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
            );
        }
        if (Build.VERSION.SDK_INT >= 21) {
            setWindowFlag(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS
                    | WindowManager.LayoutParams.FLAG_TRANSLUCENT_NAVIGATION, false);
            getWindow().setStatusBarColor(Color.TRANSPARENT);
            getWindow().setNavigationBarColor(Color.TRANSPARENT);
        }
    }

    private void setWindowFlag(final int bits, boolean on) {
        Window win = getWindow();
        WindowManager.LayoutParams winParams = win.getAttributes();
        if (on) {
            winParams.flags |= bits;
        } else {
            winParams.flags &= ~bits;
        }
        win.setAttributes(winParams);
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
        int appVersionCode = Integer.parseInt(appFunctions.getJsonValue("APP_VERSION_CODE", appFunctions.retrieveValue(Utils.APP_GENERAL_DATA)));

        if(isForceUpdate.equalsIgnoreCase("Yes") && appFunctions.getAppVersionCode() < appVersionCode){//&& appFunctions.getAppVersionCode() < appVersionCode
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