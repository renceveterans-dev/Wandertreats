package com.wandertech.wandertreats;

import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.cardview.widget.CardView;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.DialogFragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.appbar.AppBarLayout;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.bottomsheet.BottomSheetDialogFragment;
import com.google.android.material.snackbar.Snackbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.google.common.cache.CacheBuilderSpec;
import com.wandertech.wandertreats.adapter.RadioSelctionAdapter;
import com.wandertech.wandertreats.databinding.ActivityForgotPasswordBinding;
import com.wandertech.wandertreats.databinding.ActivityPurchasePreviewBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.PaymentBottomSheetFragment;
import com.wandertech.wandertreats.general.PopUpDialog;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;

import java.util.ArrayList;
import java.util.HashMap;

public class PurchasePreviewActivity extends AppCompatActivity implements  AppBarLayout.OnOffsetChangedListener,  RadioSelctionAdapter.ItemOnClickListener{

    private ActivityPurchasePreviewBinding binding;
    private View contentView;
    private GeneralFunctions appFunctions;
    private final Handler handler = new Handler();
    private AppCompatTextView titleTxt, forgotPasswordTxt;
    private MaterialToolbar materialToolbar;
    private AppBarLayout appBarLayout;
    private ImageView backImgView;
    private LinearLayoutCompat productArea;
    private RecyclerView paymentRecyclerView;
    private AppCompatTextView add, minus, qtyText;
    private CardView addImgView, minusImgView;
    private String productData;
    private AppCompatTextView productName, productDesc, productPrice;
    private TextInputLayout fNameTxtLayout, lNameTxtLayout, emailTxtLayout, mobileTxtLayout;
    private TextInputEditText fNameTxt, lNameTxt, emailTxt, mobileTxt;
    private AppCompatButton payAtStoreBtn;
    private AppCompatButton payBtn;
    private AppCompatTextView totalAmountTxt;
    private String previewProductData;
    private double totalAmount = 0.0;


    public RadioSelctionAdapter radioSelctionAdapter;
    public ArrayList<HashMap<String, String>> dataList;
    public JSONArray paymentMethodArr = new JSONArray();
    public String paymentData = "";

    public int selected = 111;
    public boolean hasSelected = false;
    public HashMap<String, String> parameters = new HashMap<String, String>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityPurchasePreviewBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
        appFunctions.setWindowFlag((Activity) getActContext(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.transparent));
        }


        initView();
        setLabel();

        productData = getIntent().getStringExtra("data");


        paymentData = appFunctions.retrieveValue(Utils.APP_GENERAL_DATA);
        paymentMethodArr = appFunctions.getJsonArray("paymentMethods", paymentData);

        dataList = Data.getPaymentData(paymentMethodArr, appFunctions);
        // appFunctions.showMessage( dataList.toString());
        radioSelctionAdapter = new RadioSelctionAdapter(getActContext(), dataList);
        paymentRecyclerView.setLayoutManager(new LinearLayoutManager(getActContext()));
        paymentRecyclerView.setAdapter(radioSelctionAdapter);
        radioSelctionAdapter.setOnItemClick(this::setOnItemClick);

        loadPurchasePreview();

    }

    public void loadPurchasePreview() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "PURCHASE_PREVIEW");
        parameters.put("userId",  appFunctions.getMemberId());
        parameters.put("productId", appFunctions.getJsonValue("iProductId", productData) );
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_purchase_preview.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        String userData = appFunctions.getJsonValue("userData", responseString);

                        previewProductData = appFunctions.getJsonValue("previewProductData", responseString);
                        productName.setText(appFunctions.getJsonValue("vProductName", previewProductData));
                        productDesc.setText("");//appFunctions.getJsonValue("vDescription", previewProductData)
                        productPrice.setText(appFunctions.getCurrencySymbol()+appFunctions.getJsonValue("fPrice", previewProductData));

                        fNameTxt.setText(appFunctions.getJsonValue("vName", userData));
                        lNameTxt.setText(appFunctions.getJsonValue("vLastName", userData));
                        emailTxt.setText(appFunctions.getJsonValue("vEmail", userData));
                        mobileTxt.setText(appFunctions.getJsonValue("vPhone", userData));

                        computTotalAmount();

                       // Toast.makeText(getActContext(), "ProductId : "+appFunctions.getJsonValue("iProductId",previewProductData), Toast.LENGTH_SHORT).show();

                    }else{


                    }

                }

                //
            }
        });
        exeWebServer.execute();
    }

    private void initView() {

        titleTxt = binding.  titleTxt;
        appBarLayout = binding.appBarLayout;
        materialToolbar = binding.toolbar;
        backImgView = binding.backImgView;


        ViewGroup.MarginLayoutParams params = (ViewGroup.MarginLayoutParams) materialToolbar.getLayoutParams();
        params.topMargin = getStatusBarHeight();
        materialToolbar.setLayoutParams(params);

        productName = binding.productName;
        productDesc = binding.productDesc;
        productPrice = binding.productPrice;

        fNameTxt = binding.fNameTxt;
        lNameTxt = binding.lNameTxt;
        emailTxt = binding.emailTxt;
        mobileTxt = binding.mobileTxt;

        fNameTxtLayout = binding.fNameTxtLayout;
        lNameTxtLayout = binding.lNameTxtLayout;
        emailTxtLayout = binding.emailTxtLayout;
        mobileTxtLayout = binding.mobileTxtLayout;

        addImgView = binding.addImgView;
        minus = binding.minus;
        add = binding.add;
        minusImgView = binding.minusImgView;
        qtyText = binding.qtyText;
        totalAmountTxt = binding.totalAmountTxt;

        paymentRecyclerView = binding.paymentRecyclerView;

        payBtn = binding.payBtn;
        payAtStoreBtn = binding.payAtStoreBtn;

        appBarLayout.addOnOffsetChangedListener(this::onOffsetChanged);
        addImgView.setOnClickListener(new setOnClickAct());
        minus.setOnClickListener(new setOnClickAct());
        minusImgView.setOnClickListener(new setOnClickAct());
        add.setOnClickListener(new setOnClickAct());

        payAtStoreBtn.setOnClickListener(new setOnClickAct());
        payBtn.setOnClickListener(new setOnClickAct());
        materialToolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
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

    private void setLabel() {

        titleTxt.setText("Checkout");
        qtyText.setText("1");
    }

    @Override
    public void onOffsetChanged(AppBarLayout appBarLayout, int verticalOffset) {

        if (Math.abs(verticalOffset)-appBarLayout.getTotalScrollRange() == 0) {
            //  Collapsed

            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.white));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.black));
            titleTxt.setText("Checkout");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.black));


            getWindow().setStatusBarColor(ContextCompat.getColor(this,R.color.appThemeColor));

        } else {
            //Expanded

            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.fui_transparent));
            materialToolbar.setNavigationIconTint(getActContext().getResources().getColor(R.color.white));
            titleTxt.setText("");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.white));

            getWindow().setStatusBarColor(ContextCompat.getColor(this,R.color.transparent));
        }
    }

    @Override
    public void setOnItemClick(int position) {

        if(selected != position){
            selected = position;
            hasSelected = true;
        }else{
            selected = 111;
            hasSelected = false;
        }

        ArrayList<HashMap<String, String>> tempData = new ArrayList<>();
        for(int i = 0;i<dataList.size();i++){

            if(i != selected ){
                HashMap<String, String> map = new HashMap<>();
                map.put("title",  dataList.get(i).get("title"));
                map.put("message", dataList.get(i).get("message"));
                map.put("selected", "No");
                map.put("data",  dataList.get(i).get("data"));
                tempData.add(map);
            }else{
                HashMap<String, String> map = new HashMap<>();
                map.put("title",  dataList.get(i).get("title"));
                map.put("message", dataList.get(i).get("message"));
                map.put("selected", "Yes");
                map.put("data", dataList.get(i).get("data"));
                tempData.add(map);
            }

        }

        dataList.clear();
        dataList = tempData;

        //radioSelctionAdapter.notifyDataSetChanged();

        radioSelctionAdapter = new RadioSelctionAdapter(getActContext(), tempData);
        paymentRecyclerView.setLayoutManager(new LinearLayoutManager(getActContext()));
        paymentRecyclerView.setAdapter(radioSelctionAdapter);
        radioSelctionAdapter.setOnItemClick(this::setOnItemClick);

    }

    public class setOnTextChangeAct implements TextWatcher {
        public TextInputEditText editText;
        public setOnTextChangeAct(TextInputEditText editText){
            this.editText = editText;
        }
        @Override
        public void beforeTextChanged(CharSequence s, int start, int count, int after) {
        }
        @Override
        public void onTextChanged(CharSequence s, int start, int before, int count) {


        }

        @Override
        public void afterTextChanged(Editable s) {
            switch (editText.getId()) {

                default:

                    break;

            }
        }
    }

    public void payNow() {

        try{

            HashMap<String, String> parameters = new HashMap<String, String>();
            parameters.put("type", "PAY_AT THE_STORE");
            parameters.put("userId", appFunctions.getMemberId());
            parameters.put("firstName", fNameTxt.getText().toString() == null ? "" : fNameTxt.getText().toString());;
            parameters.put("lastName", lNameTxt.getText().toString() == null ? "" : lNameTxt.getText().toString() );
            parameters.put("email", emailTxt.getText().toString() == null ? "" : emailTxt.getText().toString() );
            parameters.put("mobileNumber", mobileTxt.getText().toString() == null ? "" : mobileTxt.getText().toString() );
            parameters.put("totalAmount", totalAmount +"");
            parameters.put("qty", qtyText.getText().toString().trim()+"");
            parameters.put("productId", appFunctions.getJsonValue("iProductId", previewProductData));
            parameters.put("userType", Utils.app_type);

            ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_purchase_now.php", true);
            exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
            exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
                @Override
                public void setResponse(String responseString) {
                    if(responseString != null){

                        if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                            Bundle bn =  new Bundle();
                            bn.putString("purchasedId", appFunctions.getJsonValue("purchaseId",responseString));
                            bn.putString("purchasedId", appFunctions.getJsonValue("purchaseNo",responseString));
                            bn.putString("data",  appFunctions.getJsonValue("data",responseString));
                            bn.putString("isStart",  "checkout");
                            new StartActProcess(getActContext()).startActWithData(GCashPaymentActivity.class,bn);

                        }else{
                            appFunctions.showMessage(appFunctions.getJsonValue("error",responseString));

                        }



                    }

                }
            });
            exeWebServer.execute();
        }catch (Exception e){
            appFunctions.showMessage(e.toString());
        }



    }

//
//    public void payNow() {
//
//        HashMap<String, String> parameters = new HashMap<String, String>();
//        parameters.put("type", "PAY_NOW");
//        parameters.put("userId", appFunctions.getMemberId());
//        parameters.put("firstName", fNameTxt.getText().toString() == null ? "" : fNameTxt.getText().toString());;
//        parameters.put("lastName", lNameTxt.getText().toString() == null ? "" : lNameTxt.getText().toString() );
//        parameters.put("email", emailTxt.getText().toString() == null ? "" : emailTxt.getText().toString() );
//        parameters.put("mobileNumber", mobileTxt.getText().toString() == null ? "" : mobileTxt.getText().toString() );
//        parameters.put("totalAmount", totalAmount +"");
//        parameters.put("qty", qtyText.getText().toString().trim()+"");
//        parameters.put("productId", appFunctions.getJsonValue("iProductId", previewProductData));
//        parameters.put("userType", Utils.app_type);
//
//        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_purchase_now.php", true);
//        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
//        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
//            @Override
//            public void setResponse(String responseString) {
//                if(responseString != null){
//
//                    Bundle bn =  new Bundle();
//                    bn.putString("data", responseString);
//                    new StartActProcess(getActContext()).startActWithData(GCashPaymentActivity.class,bn);
//                    finish();
//                }
//            }
//
//        });
//        exeWebServer.execute();
//    }


    public void payAtTheStore() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "PAY_AT THE_STORE");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("firstName", fNameTxt.getText().toString() == null ? "" : fNameTxt.getText().toString());;
        parameters.put("lastName", lNameTxt.getText().toString() == null ? "" : lNameTxt.getText().toString() );
        parameters.put("email", emailTxt.getText().toString() == null ? "" : emailTxt.getText().toString() );
        parameters.put("mobileNumber", mobileTxt.getText().toString() == null ? "" : mobileTxt.getText().toString() );
        parameters.put("totalAmount", totalAmount +"");
        parameters.put("qty", qtyText.getText().toString().trim()+"");
        parameters.put("productId", appFunctions.getJsonValue("iProductId", previewProductData));
        parameters.put("userType", Utils.app_type);


        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_purchase.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {



                if(responseString != null){

                    Bundle bn =  new Bundle();
                    bn.putString("paymentType", "PAY_AT THE_STORE");
                    bn.putString("data", appFunctions.getJsonValue("data", responseString));
                    new StartActProcess(getActContext()).startActWithData(PurchaseConfirmationActivity.class,bn);
                    finish();

                }

            }
        });
        exeWebServer.execute();
    }

    public class setOnClickAct implements View.OnClickListener {

        private static final long MIN_CLICK_INTERVAL=600;
        private long mLastClickTime;

        @Override
        public void onClick(View view) {

            long currentClickTime= SystemClock.uptimeMillis();
            long elapsedTime=currentClickTime-mLastClickTime;

            mLastClickTime=currentClickTime;

            int qty = 0;

            if(elapsedTime<=MIN_CLICK_INTERVAL)
                return;

            switch(view.getId()){

                case R.id. payBtn:

                    if(hasSelected){
                        payNow();
                    }else{
                        appFunctions.showMessage("Please select payment method.");
                    }


//                    HashMap<String, String> parameters = new HashMap<String, String>();
//                    parameters.put("type", "PAY_AT THE_STORE");
//                    parameters.put("userId", appFunctions.getMemberId());
//                    parameters.put("firstName", fNameTxt.getText().toString() == null ? "" : fNameTxt.getText().toString());;
//                    parameters.put("lastName", lNameTxt.getText().toString() == null ? "" : lNameTxt.getText().toString() );
//                    parameters.put("email", emailTxt.getText().toString() == null ? "" : emailTxt.getText().toString() );
//                    parameters.put("mobileNumber", mobileTxt.getText().toString() == null ? "" : mobileTxt.getText().toString() );
//                    parameters.put("totalAmount", totalAmount +"");
//                    parameters.put("qty", qtyText.getText().toString().trim()+"");
//                    parameters.put("productId", appFunctions.getJsonValue("iProductId", previewProductData));
//                    parameters.put("userType", Utils.app_type);
//
//                    PaymentBottomSheetFragment paymentDialogFragment = new PaymentBottomSheetFragment(getActContext(), parameters, appFunctions);
//                    paymentDialogFragment.setStyle(DialogFragment.STYLE_NORMAL, R.style.AppBottomSheetDialogTheme);
//                    paymentDialogFragment.show(getSupportFragmentManager(), "");

                    break;

                case R.id. payAtStoreBtn:

                    payAtTheStore();

                    break;

                case R.id.add:

                    addImgView.performClick();

                    break;

                case R.id.addImgView:

                     qty = Integer.parseInt(qtyText.getText().toString());
                    if(qty>0){
                        qty = qty+1;
                        qtyText.setText(qty +"");
                    }

                    computTotalAmount();

                    break;

                case R.id.minus:
                    minusImgView.performClick();
                    break;

                case R.id.minusImgView:


                    qty = Integer.parseInt(qtyText.getText().toString());
                    if(qty>1){
                        qty = qty-1;
                        qtyText.setText(qty +"");
                    }

                    computTotalAmount();

                    break;

                default:
                    break;



            }

        }


    }

    public void computTotalAmount(){

        double fprice = Double.parseDouble(appFunctions.getJsonValue("fPrice", previewProductData));
        int qty = Integer.parseInt(qtyText.getText().toString().trim());
        double totalAmount = fprice*qty;

        totalAmountTxt.setText(appFunctions.getDecimalWithSymbol(totalAmount));

    }



    public Context getActContext() {
        return PurchasePreviewActivity.this;
    }
}

