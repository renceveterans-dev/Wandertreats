package com.wandertech.wandertreats;

import android.app.Activity;
import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.Toast;

import com.google.android.material.appbar.AppBarLayout;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.snackbar.Snackbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.databinding.ActivityCheckoutBinding;
import com.wandertech.wandertreats.databinding.ActivityPurchasePreviewBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.cardview.widget.CardView;

public class CheckoutActivity extends AppCompatActivity implements  AppBarLayout.OnOffsetChangedListener{

    private ActivityCheckoutBinding binding;
    private View contentView;
    private GeneralFunctions appFunctions;
    private final Handler handler = new Handler();
    private AppCompatTextView titleTxt, forgotPasswordTxt;
    private MaterialToolbar materialToolbar;
    private AppBarLayout appBarLayout;
    private ImageView backImgView;
    private LinearLayoutCompat productArea;
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

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityCheckoutBinding.inflate(getLayoutInflater());
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

        payBtn = binding.payBtn;
        payAtStoreBtn = binding.payAtStoreBtn;


        appBarLayout.addOnOffsetChangedListener(this::onOffsetChanged);
        addImgView.setOnClickListener(new setOnClickAct());
        backImgView.setOnClickListener(new setOnClickAct());
        minus.setOnClickListener(new setOnClickAct());
        minusImgView.setOnClickListener(new setOnClickAct());
        add.setOnClickListener(new setOnClickAct());

        payAtStoreBtn.setOnClickListener(new setOnClickAct());
        payBtn.setOnClickListener(new setOnClickAct());
    }

    private void setLabel() {

        titleTxt.setText("Checkout");
        qtyText.setText("1");
    }

    @Override
    public void onOffsetChanged(AppBarLayout appBarLayout, int verticalOffset) {
        if (Math.abs(verticalOffset)-appBarLayout.getTotalScrollRange() == 0) {
            //  Collapsed

            backImgView.setBackgroundTintList(getActContext().getResources().getColorStateList(R.color.black));
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.white));
            titleTxt.setText("Puchase Preview");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.black));
        } else {
            //Expanded
            backImgView.setBackgroundTintList(getActContext().getResources().getColorStateList(R.color.white));
            materialToolbar.setBackgroundColor(getActContext().getResources().getColor(R.color.fui_transparent));
            titleTxt.setText("");
            titleTxt.setTextColor(getActContext().getResources().getColor(R.color.white));

        }
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


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "CHECK_EMAIL");
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_login.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Toast.makeText(getActContext(),responseString, Toast.LENGTH_SHORT).show();

                if(responseString != null){



                }

                //
            }
        });
        exeWebServer.execute();
    }

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

                case R.id.backImgView:
                    onBackPressed();
                    break;

                case R.id. payBtn:

//                    View rootView = ((Activity)getActContext()).getCurrentFocus();

                    Snackbar.make(view, "Payment Method is still unavailable", Snackbar.LENGTH_LONG).show();
                    // Responds to click on the action

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
        return CheckoutActivity.this;
    }
}

