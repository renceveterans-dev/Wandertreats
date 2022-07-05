package com.wandertech.wandertreats;

import android.app.Activity;
import android.content.Context;
import android.content.res.ColorStateList;
import android.graphics.Color;
import android.graphics.Picture;
import android.graphics.PorterDuff;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.text.Html;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.CompoundButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.ToggleButton;
import android.widget.Toolbar;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.appbar.AppBarLayout;
import com.google.android.material.appbar.CollapsingToolbarLayout;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.adapter.ImagePreviewAdapter;
import com.wandertech.wandertreats.adapter.MainAdapter;
import com.wandertech.wandertreats.adapter.MainItemAdapter;
import com.wandertech.wandertreats.databinding.ActivityProductBinding;
import com.wandertech.wandertreats.databinding.ActivityStoreBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.FavoriteUtils;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.ImagePreviewDialog;
import com.wandertech.wandertreats.general.MockData;
import com.wandertech.wandertreats.general.PopUpDialog;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.model.ItemModel;
import com.wandertech.wandertreats.model.ParentModel;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;

import java.net.URI;
import java.util.ArrayList;
import java.util.HashMap;

import androidx.viewpager.widget.ViewPager;
import info.androidhive.fontawesome.FontTextView;

public class ProductActivity extends AppCompatActivity implements AppBarLayout.OnOffsetChangedListener, ImagePreviewAdapter.OnItemClickListener {

    private @NonNull ActivityProductBinding binding;
    private View contentView;
    private final Handler handler = new Handler();
    private AppCompatTextView titleTxt;
    private GeneralFunctions appFunctions;
    private TextInputLayout usernameTxtLayout, passwordTxtLayout;
    private TextInputEditText usernameTxt, passwordTxt;
    private CollapsingToolbarLayout collapsing_toolbar;
    private AppBarLayout appBarLayout;
    private LinearLayoutCompat productArea;
    private LinearLayoutCompat productImageArea;
    private MaterialToolbar materialToolbar;
    private AppCompatImageView shareBtn;
    private ArrayList<ParentModel> mainArr = new ArrayList<>();
    private RecyclerView productsRecyclerList;
    private MainAdapter mainAdapter;
    private AppCompatButton buyBtn;
    private AppCompatTextView productName,productLabel, productDescription,
            productPriceTxt, storeLocationVTxt, rulesVTxt, claimInstructionTxt;
    private ImageView productImage;
    private String productData = "";
    private String productId = "";
    private ViewPager viewpagerImagePreview;
    private TextView[] imagePreviewIndicator;
    private LinearLayout imagePreviewIndicatorLayout;
    private ImagePreviewAdapter imagePreviewAdapter;
    private ArrayList<String> imagelist = new ArrayList<>();
    private AppCompatTextView noteText;
    private RecyclerView simillarRecyclerList;
    private ToggleButton favoriteBtn;
    private FavoriteUtils favoriteUtils;


    public String filePath = Constants.SERVER+"uploads/products/";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityProductBinding.inflate(getLayoutInflater());
        favoriteUtils = new FavoriteUtils(getActContext(), appFunctions);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
        appFunctions.setWindowFlag((Activity) getActContext(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent));
        }

        setContentView(binding.getRoot());

        productData = getIntent().getStringExtra("data");
        productId = appFunctions.getJsonValue("iProductId", productData);

        setInitView();
        setEventListeners();

        loadProduct();
        setFavoriteItem();

    }

    private void setEventListeners() {

        appBarLayout.addOnOffsetChangedListener(this::onOffsetChanged);
        buyBtn.setOnClickListener(new setOnClickAct());

        productName.setText(appFunctions.getJsonValue("vProductName", productData));
        productLabel.setText(appFunctions.getJsonValue("vUserName", productData));
        productPriceTxt.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fPrice", productData)));
        productDescription.setText(appFunctions.getJsonValue("vProductDesc", productData));
        storeLocationVTxt.setText(appFunctions.getJsonValue("vStoreAddress", productData));
        rulesVTxt.setText(Html.fromHtml(appFunctions.getJsonValue("vTerms", productData)));
        claimInstructionTxt.setText(Html.fromHtml(appFunctions.getJsonValue("vHowToClaim", productData)));
        materialToolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

        try{
            imagelist = Data.getProductImages(appFunctions.getJsonArray("vImages",productData), appFunctions);
        }catch (Exception e){
            appFunctions.showMessage(e.toString());
        }

        ColoredBars(0);
        imagePreviewAdapter = new ImagePreviewAdapter(getActContext(), imagelist);
        viewpagerImagePreview.setAdapter(imagePreviewAdapter);
        imagePreviewAdapter.setOnItemClickListener(this);
        viewpagerImagePreview.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {

            @Override
            public void onPageSelected(int position) {
                ColoredBars(position);
                if (position == imagelist.size()- 1) {

                } else {

                }
            }

            @Override
            public void onPageScrolled(int arg0, float arg1, int arg2) {

            }

            @Override
            public void onPageScrollStateChanged(int arg0) {

            }
        });

    }

    private void setInitView() {

        titleTxt = binding.  titleTxt;
        appBarLayout = binding.appBarLayout;
        materialToolbar = binding.toolbar;

        ViewGroup.MarginLayoutParams params = (ViewGroup.MarginLayoutParams) materialToolbar.getLayoutParams();
        params.topMargin = getStatusBarHeight();
        materialToolbar.setLayoutParams(params);

        productArea = binding.productArea;
        shareBtn = binding.shareBtn;
        buyBtn = binding.buyBtn;

        productName = binding.productName;
        productLabel = binding.productLabel;
        productPriceTxt = binding.productPriceTxt;
        productDescription = binding.productDescription;
        rulesVTxt = binding.rulesVTxt;
        storeLocationVTxt = binding.storeLocationVTxt;
        productImageArea = binding.productImageArea;
        productImage = binding.productImage;
        viewpagerImagePreview = binding.viewpagerImagePreview;
        imagePreviewIndicatorLayout = binding.imagePreviewIndicatorLayout;
        claimInstructionTxt = binding.claimInstructionTxt;
        noteText = binding.noteText;
        favoriteBtn = binding.favoriteBtn;
        simillarRecyclerList = binding.simillarRecyclerList;
    }

    private void setFavoriteItem() {

        if(favoriteUtils.isFavorite(productId)){
            favoriteBtn.setChecked(true);
        }else{
            favoriteBtn.setChecked(false);
        }

        favoriteBtn.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    favoriteUtils.addToFavoritemItemList(appFunctions.getJsonValue("iProductId", productData));
                    appFunctions.showMessage("Favarite!");
                } else {
                    favoriteUtils.removeToFavoritemItemList(appFunctions.getJsonValue("iProductId", productData));
                    appFunctions.showMessage("Umfavarite.");
                }


            }
        });
    }

    public int getStatusBarHeight(){
        int statusBarHeight = 0;
        int resourceId = getResources().getIdentifier("status_bar_height", "dimen", "android");
        if (resourceId > 0) {
            statusBarHeight = getResources().getDimensionPixelSize(resourceId);
        }

        return statusBarHeight;
    }

    private void ColoredBars(int thisScreen) {

        int[] colorsInactive = getResources().getIntArray(R.array.array_slider_inactive);
        int[] colorsActive = getResources().getIntArray(R.array.array_slider_active);
        imagePreviewIndicator = new TextView[imagelist.size()];

        imagePreviewIndicatorLayout.removeAllViews();
        for (int i = 0; i < imagePreviewIndicator.length; i++) {
            imagePreviewIndicator[i] = new TextView(getActContext());
            imagePreviewIndicator[i].setTextSize(35);
            imagePreviewIndicator[i].setText(Html.fromHtml("&#8226;"));
            imagePreviewIndicatorLayout.addView(imagePreviewIndicator[i]);
            imagePreviewIndicator[i].setTextColor(colorsInactive[thisScreen]);
        }
        if (imagePreviewIndicator.length > 0)
            imagePreviewIndicator[thisScreen].setTextColor(colorsActive[thisScreen]);

    }


    public Context getActContext() {
        return ProductActivity.this;
    }

    @Override
    public void onOffsetChanged(AppBarLayout appBarLayout, int verticalOffset) {
        if (Math.abs(verticalOffset)-appBarLayout.getTotalScrollRange() == 0) {
            //  Collapsed

            productArea.setVisibility(View.INVISIBLE);
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.white));
            materialToolbar.setNavigationIconTint(ContextCompat.getColor(ProductActivity.this,R.color.appThemeColor));
            titleTxt.setText("STORE");
            titleTxt.setText(appFunctions.getJsonValue("vProductName", productData));
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.black));

            getWindow().setStatusBarColor(ContextCompat.getColor(ProductActivity.this,R.color.appThemeColor));


        } else {
            //Expanded
            productArea.setVisibility(View.VISIBLE);
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.fui_transparent));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.white));
            titleTxt.setText("");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.white));

            getWindow().setStatusBarColor(ContextCompat.getColor(ProductActivity.this,R.color.transparent));
        }
    }

    public void loadProduct() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_PRODUCT");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("iProductId", appFunctions.getJsonValue("iProductId", productData));
        parameters.put("iMerchantId", appFunctions.getJsonValue("iMerchantId", productData));
        parameters.put("userType", Utils.app_type);
        //appFunctions.showMessage(parameters.toString());

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_products.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        String data = appFunctions.getJsonValue("data", responseString);

                        productData = appFunctions.getJsonValue("productData", data);

                        productName.setText(appFunctions.getJsonValue("vProductName", productData));
                        productLabel.setText(appFunctions.getJsonValue("vUserName", productData));
                        productPriceTxt.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fPrice", productData)));
                        productDescription.setText(appFunctions.getJsonValue("vProductDesc", productData));
                        storeLocationVTxt.setText(appFunctions.getJsonValue("vStoreAddress", productData));
                        rulesVTxt.setText(Html.fromHtml(appFunctions.getJsonValue("vTerms", productData)));
                        claimInstructionTxt.setText(Html.fromHtml(appFunctions.getJsonValue("vHowToClaim", productData)));

                        boolean buyButtonEnable = Boolean.getBoolean(appFunctions.getJsonValue("buyButtonEnable", responseString));
                        if(appFunctions.getJsonValue("buyButtonEnable", responseString).equalsIgnoreCase("true")){

                            noteText.setVisibility(View.GONE);

                            buyBtn.setBackgroundTintList(ColorStateList.valueOf(getActContext().getResources().getColor(R.color.appThemeColor)));
                            buyBtn.setEnabled(true);
                            //buyBtn.getBackground().setColorFilter(Color.GRAY, PorterDuff.Mode.LIGHTEN);
                        }else{

                            noteText.setVisibility(View.VISIBLE);
                            noteText.setText("You have still unclaimed item of this product. ");
                            buyBtn.setBackgroundTintList(ColorStateList.valueOf(getActContext().getResources().getColor(R.color.gray)));
                            buyBtn.setEnabled(false);

                        }

                        ArrayList<ItemModel> productArrayList = new ArrayList<>();
                        productArrayList = Data.getProductData(appFunctions.getJsonArray("simillarProductData",  data), appFunctions);

                        //appFunctions.showMessage(appFunctions.getJsonArray("productData", storeData).toString());

                        MainItemAdapter mainItemAdapter = new MainItemAdapter(productArrayList, getActContext());
                        simillarRecyclerList.setLayoutManager(new LinearLayoutManager(getActContext(), LinearLayoutManager.HORIZONTAL, false));
                        simillarRecyclerList.setAdapter(mainItemAdapter);


                    }else{

                    }

                }

                //
            }
        });
        exeWebServer.execute();
    }


    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }

    @Override
    public void onItemClickList(int position, String image) {

        //appFunctions.showMessage(filePath+image);
        try{
         ImagePreviewDialog imagePreviewDialog = new ImagePreviewDialog(getActContext());
                   imagePreviewDialog .createPreview( image);
            imagePreviewDialog.show();

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

                case R.id. buyBtn:

                    try{
                        Bundle bn = new Bundle();

                        bn.putInt("SCAN_MODE", 11);
                        bn.putString("data", productData);
                        new StartActProcess(getActContext()).startActWithData(PurchasePreviewActivity.class, bn);

                    }catch (Exception e){
                        appFunctions.showMessage(e.toString());
                    }


                    break;
                case R.id.backImgView:

                    onBackPressed();

                    break;
                case R.id.homeBtn:

                    new StartActProcess(getActContext()).startAct(MainActivity.class);

                    break;

                case R.id.storeBtn:
                    new StartActProcess(getActContext()).startAct(StoreActivity.class);

                    break;

                case R.id.productBtn:

                    new StartActProcess(getActContext()).startAct(ProductActivity.class);
                    break;

            }

        }
    }
}


