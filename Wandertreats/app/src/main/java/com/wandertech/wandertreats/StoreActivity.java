package com.wandertech.wandertreats;

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
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.appbar.AppBarLayout;
import com.google.android.material.appbar.CollapsingToolbarLayout;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.adapter.GridCategoryAdapter;
import com.wandertech.wandertreats.adapter.MainAdapter;
import com.wandertech.wandertreats.adapter.MainItemAdapter;
import com.wandertech.wandertreats.databinding.ActivityStoreBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.MockData;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.model.ItemModel;
import com.wandertech.wandertreats.model.ParentModel;
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

import de.hdodenhof.circleimageview.CircleImageView;
import info.androidhive.fontawesome.FontTextView;

public class StoreActivity extends AppCompatActivity implements  AppBarLayout.OnOffsetChangedListener{

    private ActivityStoreBinding binding;
    private View contentView;
    private final Handler handler = new Handler();
    private AppCompatTextView titleTxt;
    private LinearLayoutCompat getDirectionsBtn;
    private GeneralFunctions appFunctions;
    private TextInputLayout usernameTxtLayout, passwordTxtLayout;
    private TextInputEditText usernameTxt, passwordTxt;
    private CollapsingToolbarLayout collapsing_toolbar;
    private AppBarLayout appBarLayout;
    private MaterialToolbar materialToolbar;
    private ImageView backImgView;
    private LinearLayoutCompat storeInfoArea;
    private ArrayList<ParentModel> mainArr = new ArrayList<>();
    private RecyclerView productsRecyclerList;
    private MainAdapter mainAdapter;
    private String storeData = "";
    private String productData = "";
    private MapView map;
    private CircleImageView logoImage;
    private ImageView storeImage;
    private GeoPoint storePointLocation;
    private IMapController mapController;
    private ItemizedIconOverlay<OverlayItem> anotherItemizedIconOverlay;
    private ArrayList<OverlayItem> markerArray = new ArrayList<>();
    private AppCompatTextView storeName, storeLabel, storeLocationValueTxt;
    public String filePath = Constants.SERVER+"uploads/store/";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
        appFunctions.setWindowFlag((Activity) getActContext(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent));
        }

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityStoreBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        storeData = getIntent().getStringExtra("data");
        productData = getIntent().getStringExtra("data");

        initView();
        setLabel();
        initMap();

    }

    private void initView() {

        titleTxt = binding.  titleTxt;
        appBarLayout = binding.appBarLayout;
        materialToolbar = binding.toolbar;

        ViewGroup.MarginLayoutParams params = (ViewGroup.MarginLayoutParams) materialToolbar.getLayoutParams();
        params.topMargin = appFunctions.getStatusBarHeight();
        materialToolbar.setLayoutParams(params);

        storeInfoArea = binding.storeInfoArea;
        productsRecyclerList = binding.productsRecyclerList;

        getDirectionsBtn = binding.getDirectionsBtn;

        storeName = binding.storeName;
        storeLabel = binding.storeLabel;
        storeLocationValueTxt = binding.storeLocationValueTxt;

        logoImage = binding.logoImage;
        storeImage = binding.storeImage;

        getDirectionsBtn.setOnClickListener(new setOnClickAct());
        appBarLayout.addOnOffsetChangedListener(this::onOffsetChanged);
        materialToolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

    }


    private void setLabel() {

        titleTxt.setText("Log In");
        storeName.setText(appFunctions.getJsonValue("vStoreName", storeData));
        storeLabel.setText(appFunctions.getJsonValue("vUsername", storeData));
        storeLocationValueTxt.setText(appFunctions.getJsonValue("vStoreLocation", storeData));

        Picasso.with(getActContext())
                .load(appFunctions.getJsonValue("vLogo", storeData))
                .placeholder(R.color.shimmer_placeholder)
                .into(logoImage);

        Picasso.with(getActContext())
                .load(appFunctions.getJsonValue("vImages", storeData))
                .placeholder(R.color.shimmer_placeholder)
                .into( storeImage);


        ArrayList<ItemModel> productArrayList = new ArrayList<>();
        productArrayList = Data.getProductData(appFunctions.getJsonArray("productData", storeData), appFunctions);

        //appFunctions.showMessage(appFunctions.getJsonArray("productData", storeData).toString());

        MainItemAdapter mainItemAdapter = new MainItemAdapter(productArrayList, getActContext());
        productsRecyclerList.setLayoutManager(new LinearLayoutManager(getActContext(), LinearLayoutManager.HORIZONTAL, false));
        productsRecyclerList.setAdapter(mainItemAdapter);

    }

    public void initMap() {
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

//    public class setOnTextChangeAct implements TextWatcher {
//        public TextInputEditText editText;
//        public setOnTextChangeAct(TextInputEditText editText){
//            this.editText = editText;
//        }
//        @Override
//        public void beforeTextChanged(CharSequence s, int start, int count, int after) {
//        }
//        @Override
//        public void onTextChanged(CharSequence s, int start, int before, int count) {
//
//            usernameTxtLayout.setHelperText(null);
//            usernameTxtLayout.setHelperTextEnabled(false);
//            usernameTxtLayout.setErrorEnabled(false);
//
//            passwordTxtLayout.setHelperText(null);
//            passwordTxtLayout.setHelperTextEnabled(false);
//            passwordTxtLayout.setErrorEnabled(false);
//        }
//
//        @Override
//        public void afterTextChanged(Editable s) {
//            switch (editText.getId()) {
//
//                case R.id.usernametxt:
//
//                    if(usernameTxt.getText().length() == 0 ){
//
//                        usernameTxtLayout.setErrorEnabled(true);
//                        usernameTxtLayout.setHelperTextEnabled(true);
//                        usernameTxtLayout.setHelperText("This field is required.");
//
//                        return;
//                    }
//
//                    break;
//
//                case R.id.passwordTxt:
//
//                    if(passwordTxt.getText().length() == 0 ){
//
//                        passwordTxtLayout.setErrorEnabled(true);
//                        passwordTxtLayout.setHelperTextEnabled(true);
//                        passwordTxtLayout.setHelperText("This field is required.");
//
//                        return;
//                    }
//
////                    usernameTxtLayout.setHelperText(null);
////                    usernameTxtLayout.setHelperTextEnabled(false);
////                    usernameTxtLayout.setErrorEnabled(false);
////
////                    passwordTxtLayout.setHelperText(null);
////                    passwordTxtLayout.setHelperTextEnabled(false);
////                    passwordTxtLayout.setErrorEnabled(false);
//
//                    break;
//
//            }
//        }
//    }
//


    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }

    public Context getActContext() {
        return StoreActivity.this;
    }

    @Override
    public void onOffsetChanged(AppBarLayout appBarLayout, int verticalOffset) {

        if (Math.abs(verticalOffset)-appBarLayout.getTotalScrollRange() == 0) {

            storeInfoArea.setVisibility(View.INVISIBLE);
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.white));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.black));
            titleTxt.setText("STORE");
            titleTxt.setText( appFunctions.getJsonValue("vStoreName", storeData));
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.black));

            getWindow().setStatusBarColor(ContextCompat.getColor(this,R.color.appThemeColor));

        } else {
            //Expanded
            storeInfoArea.setVisibility(View.VISIBLE);
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.fui_transparent));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.white));
            titleTxt.setText("");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.white));

            getWindow().setStatusBarColor(ContextCompat.getColor(this,R.color.transparent));

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

                case R.id.backImgView:

                    onBackPressed();

                    break;

                case R.id.searchTxt:
                    new StartActProcess(getActContext()).startAct(SearchActivity.class);
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

                case R.id.getDirectionsBtn:

                    bn.putString("data", storeData);
                    new StartActProcess(getActContext()).startActForResult(GetDriectionActivity.class, bn, Constants.LOCATION_PICKER);


                    break;

            }

        }
    }
}


