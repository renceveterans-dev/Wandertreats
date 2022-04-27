package com.wandertech.wandertreats;

import android.app.Activity;
import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.appbar.AppBarLayout;
import com.google.android.material.appbar.CollapsingToolbarLayout;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.adapter.MainAdapter;
import com.wandertech.wandertreats.databinding.ActivityPurchasedDetailsBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.model.ParentModel;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;

import java.util.ArrayList;
import java.util.HashMap;

import static com.wandertech.wandertreats.utils.Utils.RC_CLAIM_PRODUCT;

public class PurchasedDetailsActivity extends AppCompatActivity implements  AppBarLayout.OnOffsetChangedListener{

    private ActivityPurchasedDetailsBinding binding;
    private final Handler handler = new Handler();

    private View contentView;
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
    private AppCompatButton claimBtn;
    private AppCompatTextView productName,productLabel, productDetailTxt, productDescription, productTerms, claimInstructionTxt,
            productPriceTxt, storeLocationVTxt, bottomValidUntilxt;
    private ImageView productImage;
    private String purchasedData = "";
    private AppCompatTextView productQtyTxt, productDatePurchaseTxt;
    public String filePath = Constants.SERVER+"uploads/products/";
    public boolean istimerfinish = false, isAcceptingFinish = false;
    public int maxProgressValue = 900;
    public long totalTimeCountInMilliseconds = maxProgressValue* 1000;
    private ArrayList<String> imagelist = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityPurchasedDetailsBinding.inflate(getLayoutInflater());

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
        appFunctions.setWindowFlag((Activity) getActContext(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent));
        }

        setContentView(binding.getRoot());
        purchasedData = getIntent().getStringExtra("data");

        titleTxt = binding.  titleTxt;
        appBarLayout = binding.appBarLayout;
        materialToolbar = binding.toolbar;

        ViewGroup.MarginLayoutParams params = (ViewGroup.MarginLayoutParams) materialToolbar.getLayoutParams();
        params.topMargin = getStatusBarHeight();
        materialToolbar.setLayoutParams(params);

        productArea = binding.productArea;
        shareBtn = binding.shareBtn;
        claimBtn = binding.claimBtn;

        productName = binding.productName;
        productLabel = binding.productLabel;
        productDatePurchaseTxt = binding.productDatePurchaseTxt;
        productPriceTxt = binding.productPriceTxt;
        productQtyTxt = binding.productQtyTxt;
        productDetailTxt = binding.productDetailTxt;
        productDescription = binding.productDescription;
        storeLocationVTxt = binding.storeLocationVTxt;
        productImageArea = binding.productImageArea;
        productImage = binding.productImage;
        bottomValidUntilxt = binding.bottomValidUntilxt;
        productTerms = binding.productTerms;
        claimInstructionTxt = binding.claimInstructionTxt;

        appBarLayout.addOnOffsetChangedListener(this::onOffsetChanged);

        claimBtn.setOnClickListener(new PurchasedDetailsActivity.setOnClickAct());
        materialToolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

        init();
        //loadProduct();
    }

    public int getStatusBarHeight(){
        int statusBarHeight = 0;
        int resourceId = getResources().getIdentifier("status_bar_height", "dimen", "android");
        if (resourceId > 0) {
            statusBarHeight = getResources().getDimensionPixelSize(resourceId);
        }

        return statusBarHeight;
    }

    public void init(){

        try{

            JSONArray purchaseArr = appFunctions.getJsonArray("productData", purchasedData);
            String productData = appFunctions.getJsonValue(purchaseArr, 0).toString();

            JSONArray merchantArr = appFunctions.getJsonArray("merchantData", purchasedData);
            String merchantData = appFunctions.getJsonValue(merchantArr, 0).toString();

            productName.setText(appFunctions.getJsonValue("vPurchaseName", purchasedData));
            productLabel.setText(appFunctions.getJsonValue("vPurchaseNo", purchasedData));
            bottomValidUntilxt.setText(appFunctions.getJsonValue("tPurchaseRequestDate", purchasedData));

            productPriceTxt.setText("₱"+appFunctions.getJsonValue("fNetTotal", purchasedData));
            productDetailTxt.setText(appFunctions.getJsonValue("vStoreName", purchasedData));
            productDatePurchaseTxt.setText(appFunctions.getJsonValue("tPurchaseRequestDate", purchasedData));
            productQtyTxt.setText(appFunctions.getJsonValue("iQty", productData)+"x - "+appFunctions.getJsonValue("fPrice", productData));
            storeLocationVTxt.setText(appFunctions.getJsonValue("vStoreAddress",merchantData));

            productTerms.setText(appFunctions.getJsonValue("vTerms", productData));
            claimInstructionTxt.setText(appFunctions.getJsonValue("vHowToClaim", productData));
            productDescription.setText(appFunctions.getJsonValue("vProductDesc", productData));

            Picasso.with(getActContext())
                    .load(appFunctions.getJsonValue("vThumbnail", productData))
                    .placeholder(R.color.shimmer_placeholder)
                    .into(productImage);


            if(appFunctions.getJsonValue("iStatusCode", purchasedData).equalsIgnoreCase("1")) {

                bottomValidUntilxt.setText("Claim this item until "+appFunctions.getJsonValue("tPurchaseExpiryDate", purchasedData));
                claimBtn.setText("MAKE PAYMENT");

            }else if(appFunctions.getJsonValue("iStatusCode", purchasedData).equalsIgnoreCase("2")){

                bottomValidUntilxt.setText("Claim this item until "+appFunctions.getJsonValue("tPurchaseExpiryDate", purchasedData));
                claimBtn.setText("CLAIM");

            }else if(appFunctions.getJsonValue("iStatusCode", purchasedData).equalsIgnoreCase("5")){
                claimBtn.setText("GET YOUR ITEM");

                long dRequestClaimDate = appFunctions.findDifference(appFunctions.getJsonValue("dRequestClaimDate", purchasedData));
                long endClaimTime = appFunctions.findDifference( appFunctions.getJsonValue("dRequestClaimDate", purchasedData), appFunctions.getJsonValue("claimEndTime", purchasedData));
                long remainingTime = endClaimTime - dRequestClaimDate;

                appFunctions.storeData(Utils.REMAINING_TIME,remainingTime+"");
                bottomValidUntilxt.setText(appFunctions.getJsonValue("dRequestClaimDate", purchasedData)+ " | "+String.format("%02d:%02d", (remainingTime % 3600) / 60, remainingTime% 60) +" | "+String.format("%02d:%02d", (endClaimTime % 3600) / 60, endClaimTime% 60));
                bottomValidUntilxt.setText(appFunctions.getJsonValue("dValidity", purchasedData));

            }else{
                claimBtn.setText("VIEW");
            }

        }catch (Exception e){
            appFunctions.showMessage(e.toString()+"\n"+purchasedData);
        }


    }

    public Context getActContext() {
        return PurchasedDetailsActivity.this;
    }

    @Override
    public void onOffsetChanged(AppBarLayout appBarLayout, int verticalOffset) {
        if (Math.abs(verticalOffset)-appBarLayout.getTotalScrollRange() == 0) {

            productArea.setVisibility(View.INVISIBLE);
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.white));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.black));
            titleTxt.setText("STORE");
            titleTxt.setText(appFunctions.getJsonValue("vPurchaseName", purchasedData));
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.black));

            getWindow().setStatusBarColor(ContextCompat.getColor(PurchasedDetailsActivity.this,R.color.appThemeColor));

        } else {
            //Expanded
            productArea.setVisibility(View.VISIBLE);
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.fui_transparent));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.white));
            titleTxt.setText("");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.white));

            getWindow().setStatusBarColor(ContextCompat.getColor(PurchasedDetailsActivity.this,R.color.transparent));

        }
    }

    public void loadProduct() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_PRODUCT");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("vPurchaseNo", appFunctions.getJsonValue("vPurchaseNo", purchasedData));
        parameters.put("iPurchaseId", appFunctions.getJsonValue("iPurchaseId", purchasedData));
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_single_purchased_product.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                //Toast.makeText(getActContext(),responseString, Toast.LENGTH_SHORT).show();
                if(responseString != null){
                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){
                        purchasedData = appFunctions.getJsonValue("data", responseString);
                        //appFunctions.showMessage(appFunctions.getJsonValue("iStatusCode", purchasedData));
                        init();


                    }else{

                    }

                }

                //
            }
        });
        exeWebServer.execute();
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadProduct();
    }



    @Override
    public void onBackPressed() {
        super.onBackPressed();
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

                case R.id.claimBtn:

                    try{
                        Bundle bn = new Bundle();

                        if(appFunctions.getJsonValue("iStatusCode", purchasedData).equalsIgnoreCase("1")) {

                            bn.putString("data", purchasedData);
                            bn.putString("isStart",  "details");
                            new StartActProcess(getActContext()).startActWithData(GCashPaymentActivity.class, bn);

                        } else if(appFunctions.getJsonValue("iStatusCode", purchasedData).equalsIgnoreCase("2")){

                            bn.putInt("SCAN_MODE", RC_CLAIM_PRODUCT);
                            bn.putString("data", purchasedData);
                            new StartActProcess(getActContext()).startActWithData(ScanActivity.class, bn);

                        }else{

                            bn.putInt("SCAN_MODE", RC_CLAIM_PRODUCT);
                            bn.putString("data", purchasedData);
                            new StartActProcess(getActContext()).startActWithData(ClaimActivity.class, bn);

                        }

                    }catch (Exception e){
                        appFunctions.showMessage(e.toString());
                    }



                    break;
                case R.id.homeBtn:

                    new StartActProcess(getActContext()).startAct(MainActivity.class);

                    break;

                case R.id.storeBtn:
                    new StartActProcess(getActContext()).startAct(StoreActivity.class);

                    break;

                case R.id.productBtn:

                    new StartActProcess(getActContext()).startAct(PurchasedDetailsActivity.class);
                    break;

            }

        }
    }
}


