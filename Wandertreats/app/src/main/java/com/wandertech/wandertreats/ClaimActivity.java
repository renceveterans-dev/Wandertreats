package com.wandertech.wandertreats;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;
import android.os.SystemClock;
import android.os.Vibrator;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.Toast;

import com.google.android.material.appbar.AppBarLayout;
import com.google.android.material.appbar.CollapsingToolbarLayout;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.mikhaellopez.circularprogressbar.CircularProgressBar;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.databinding.ActivityClaimBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.PopUpDialog;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.model.ParentModel;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.RecyclerView;

import org.json.JSONArray;

import java.util.ArrayList;
import java.util.HashMap;

import de.hdodenhof.circleimageview.CircleImageView;
import kotlin.Unit;

public class ClaimActivity extends AppCompatActivity implements  AppBarLayout.OnOffsetChangedListener{
    
    private ActivityClaimBinding binding;
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
    private AppCompatTextView storeNameTxt , productNameTxt, productLabel,
            productPriceTxt, storeLocationVTxt, noteTxt;
    private ImageView productImage;
    private String claimData = "";
    private String resultStoreCode = "";
    private CircularProgressBar circularProgressBar;
    private CountDownTimer countDownTimer;
    public boolean blink = false;
    public boolean istimerfinish = false, isAcceptingFinish = false;
    public int maxProgressValue = 900;
    public long totalTimeCountInMilliseconds = maxProgressValue * 1 * 1000;
    public long timeBlinkInMilliseconds = 10 * 1000;
    public String fileProductPath = Constants.SERVER+"uploads/products/";
    public String fileStorePath = Constants.SERVER+"uploads/store/";
    private AppCompatTextView timerTxt;
    private CountDownTimer timer;
    private boolean timerFinshed = true;
    private CircleImageView logoImage;
    private ArrayList<String> imagelist = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityClaimBinding.inflate(getLayoutInflater());

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
        appFunctions.setWindowFlag((Activity) getActContext(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent));
        }

        setContentView(binding.getRoot());

        //resultStoreCode = getIntent().getStringExtra("result");
        claimData = getIntent().getStringExtra("data");

        initView();

        setLabels();

        setListener();

        loadProduct();

    }

    private void setListener() {

        appBarLayout.addOnOffsetChangedListener(this::onOffsetChanged);
        materialToolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });
    }

    private void initView() {

        titleTxt = binding.  titleTxt;
        appBarLayout = binding.appBarLayout;
        materialToolbar = binding.toolbar;

        ViewGroup.MarginLayoutParams params = (ViewGroup.MarginLayoutParams) materialToolbar.getLayoutParams();
        params.topMargin = appFunctions.getStatusBarHeight();
        materialToolbar.setLayoutParams(params);

        productArea = binding.productArea;
        shareBtn = binding.shareBtn;
        timerTxt = binding.TimeCount;
        noteTxt = binding.noteTxt;

        productNameTxt = binding.productNameTxt;
        productLabel = binding.productLabel;
        storeNameTxt = binding.storeNameTxt;

        productImageArea = binding.productImageArea;
        logoImage = binding.logoImage;

        circularProgressBar = binding.circularProgressBar;

        materialToolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

    }

    @Override
    public void onOffsetChanged(AppBarLayout appBarLayout, int verticalOffset) {

        if (Math.abs(verticalOffset)-appBarLayout.getTotalScrollRange() == 0) {


            productArea.setVisibility(View.INVISIBLE);
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.white));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.black));
            titleTxt.setText("Claim");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.black));

            getWindow().setStatusBarColor(ContextCompat.getColor(this,R.color.appThemeColor));

        } else {
            //Expanded
            productArea.setVisibility(View.VISIBLE);
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.fui_transparent));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.white));
            titleTxt.setText("");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.white));

            getWindow().setStatusBarColor(ContextCompat.getColor(this,R.color.transparent));

        }
    }


    public void setLabels(){

        try{

            JSONArray merchantArr = appFunctions.getJsonArray("merchantData", claimData);
            String merchantData = appFunctions.getJsonValue(merchantArr, 0).toString();

            JSONArray purchaseArr = appFunctions.getJsonArray("productData", claimData);
            String productData = appFunctions.getJsonValue(purchaseArr, 0).toString();

            storeNameTxt.setText(appFunctions.getJsonValue("vStoreName", merchantData));
            productNameTxt.setText(appFunctions.getJsonValue("iQty", productData)+"x "+appFunctions.getJsonValue("vProductName", productData));
            productLabel.setText(appFunctions.getJsonValue("vPurchaseNo", claimData));

            Picasso.with(getActContext())
                    .load(appFunctions.getJsonValue("vThumbnail", productData))
                    .placeholder(R.color.shimmer_placeholder)
                    .into(logoImage);

            noteTxt.setText("Present this code to the store cashier.");
            titleTxt.setText("Claim");

        }catch (Exception e){
            appFunctions.showMessage("Error : "+e.toString());
        }

    }


    public void loadProduct() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_PRODUCT");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("vPurchaseNo", appFunctions.getJsonValue("vPurchaseNo", claimData));
        parameters.put("iPurchaseId", appFunctions.getJsonValue("iPurchaseId", claimData));
        parameters.put("storeId", appFunctions.getJsonValue("iMerchantId", claimData));
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_single_purchased_product.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                //Toast.makeText(getActContext(),responseString, Toast.LENGTH_SHORT).show();

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        claimData = appFunctions.getJsonValue("data", responseString);



                        setLabels();

                        long dRequestClaimDate = appFunctions.findDifference(appFunctions.getJsonValue("dRequestClaimDate", claimData));
                        long endClaimTime = appFunctions.findDifference( appFunctions.getJsonValue("dRequestClaimDate", claimData), appFunctions.getJsonValue("claimEndTime", claimData));
                        long remainingTime = endClaimTime - dRequestClaimDate;

                        //appFunctions.showMessage(appFunctions.getJsonValue("dRequestClaimDate", claimData)+" | "+ appFunctions.getJsonValue("claimEndTime", claimData)+"\n"+remainingTime);

                        if(remainingTime > 0){

                            totalTimeCountInMilliseconds = remainingTime *1*1000;
                            setTimer();

                        }else{

                            PopUpDialog dialog = new PopUpDialog(getActContext(), "Claim Failed");
                            dialog.setMessage("Claiming given time has ended.");
                            dialog.setPositiveBtn("Okay");
                            dialog.show();
                            dialog.setCancelable(false);
                            dialog.getPositive_btn().setOnClickListener(new View.OnClickListener() {
                                @Override
                                public void onClick(View v) {
                                    dialog.dismiss();
                                    Bundle bn =  new Bundle();
                                    bn.putString("isStart", "Treats");
                                    new StartActProcess(getActContext()).startActWithData(MainActivity.class,bn);
                                    finish();
                                }
                            });
                        }

                    }else{

                    }

                }

            }
        });
        exeWebServer.execute();
    }


    public void setTimer(){

        istimerfinish = false;

        countDownTimer = new CountDownTimer(totalTimeCountInMilliseconds, 1000) {
            @Override
            public void onTick(long leftTimeInMilliseconds) {
                long seconds = leftTimeInMilliseconds / 1000;

                if(!istimerfinish){
                    try{
                        timerTxt.setText(String.format(String.format("%02d:%02d",(seconds % 3600) / 60, seconds % 60)));
                    }catch ( Exception e){
                        Toast.makeText(getActContext(), "E: "+e.toString(), Toast.LENGTH_SHORT).show();
                    }

                }
            }

            @Override
            public void onFinish() {

                if(!istimerfinish ){
                    istimerfinish = true;
                }
            }

        }.start();


    }


    public Context getActContext() {
        return ClaimActivity.this;
    }
//
//    @Override
//    public void onOffsetChanged(AppBarLayout appBarLayout, int verticalOffset) {
//        if (Math.abs(verticalOffset)-appBarLayout.getTotalScrollRange() == 0) {
//            //  Collapsed
//
//            productArea.setVisibility(View.INVISIBLE);
//            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.white));
//            titleTxt.setText("STORE");
//            //titleTxt.setText(appFunctions.getJsonValue("vProductName", productData));
//            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.black));
//        } else {
//            //Expanded
//            productArea.setVisibility(View.VISIBLE);
//            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.fui_transparent));
//            titleTxt.setText("");
//            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.white));
//
//        }
//    }

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

                case R.id.backImgView:

                    onBackPressed();
                    break;

                default:

                    break;


            }

        }
    }
}


