package com.wandertech.wandertreats;

import android.content.ClipboardManager;
import android.content.Context;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.Toast;

import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatTextView;

import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.databinding.ActivityGcashPaymentBinding;
import com.wandertech.wandertreats.databinding.ActivityNotificationDetailsBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;

public class GCashPaymentActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow, copyBtn;
    private ActivityGcashPaymentBinding binding;
    private LinearLayout closeBtn;
    private AppCompatTextView titleText;
    private AppCompatTextView messageText;
    private TextInputEditText verificationCodeTxt;
    private TextInputLayout verificationCodeTxtLayout;
    private AppCompatButton submitBtn;
    private String purchasedData = "";
    private AppCompatTextView paymentAmountTxt;
    private Boolean verificationCodeChecked = false;
    private AppCompatTextView copyText;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityGcashPaymentBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        purchasedData = getIntent().getStringExtra("data");

        initView();

        setListener();

        loadData();

    }

    private void setListener() {

        verificationCodeTxt.addTextChangedListener(new setOnTextChangeAct(  verificationCodeTxt));
        submitBtn.setOnClickListener(new setOnClickAct());
        copyBtn.setOnClickListener(new setOnClickAct());

    }

    private void initView() {

        paymentAmountTxt = binding.paymentAmountTxt;
        verificationCodeTxt = binding.verificationCodeTxt;
        verificationCodeTxtLayout = binding.verificationCodeTxtLayout;
        submitBtn = binding.submitBtn;
        copyText = binding.copyText;
        copyBtn = binding.copyBtn;

    }

    private Context getActContext() {

        return GCashPaymentActivity.this;
    }

    public void loadData() {

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

                if(responseString != null){
                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        try{
                            purchasedData = appFunctions.getJsonValue("data", responseString);
                            paymentAmountTxt.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fTotalGenerateFare", purchasedData )));

                        }catch (Exception e){
                            appFunctions.showMessage(e.toString());
                        }

                    }else{

                    }

                }
            }
        });

        exeWebServer.execute();
    }

    public void submitPaymentVerification() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "SUBMIT_PAYMENT_VERIFICATION_CODE");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("purchaseId", appFunctions.getJsonValue("iPurchaseId", purchasedData));
        parameters.put("verificationCode",  verificationCodeTxt.getText().toString().trim());
        parameters.put("fAmount", appFunctions.getJsonValue("fTotalGenerateFare", purchasedData ));

        parameters.put("userType", Utils.app_type);

        //Toast.makeText(getActContext(), parameters.toString(), Toast.LENGTH_SHORT ).show();

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_verify_payment.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true, appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        //appFunctions.showMessage(responseString);
                        Bundle bn =  new Bundle();
                        bn.putString("paymentType", "GCASH_PAYMENT");
                        bn.putString("data", appFunctions.getJsonValue("data", responseString));
                        new StartActProcess(getActContext()).startActWithData(PurchaseConfirmationActivity.class,bn);
                        finish();

                    }else{
                        appFunctions.showMessage("Invalid Payment.");

                    }

                }

            }
        });
        exeWebServer.execute();
    }

    @Override
    public void onBackPressed() {


        if(getIntent().getStringExtra("isStart").equals("checkout")){
            Bundle bn =  new Bundle();
            bn.putString("isStart", "Treats");
            new StartActProcess(getActContext()).startActWithData(MainActivity.class,bn);
            finish();
        }

        if(getIntent().getStringExtra("isStart").equals("details")){
            super.onBackPressed();
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

            verificationCodeTxtLayout.setHelperText(null);
            verificationCodeTxtLayout.setHelperTextEnabled(false);
            verificationCodeTxtLayout.setErrorEnabled(false);
        }

        @Override
        public void afterTextChanged(Editable s) {

            if(verificationCodeTxt.getText().length() == 0 ){

                verificationCodeTxtLayout.setErrorEnabled(true);
                verificationCodeTxtLayout.setHelperTextEnabled(true);
                verificationCodeTxtLayout.setHelperText("This field is required.");

                return;

            }
        }
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){


                case R.id.submitBtn:

                    if(verificationCodeTxt.getText().length() == 0 ){

                        verificationCodeTxtLayout.setErrorEnabled(true);
                        verificationCodeTxtLayout.setHelperTextEnabled(true);
                        verificationCodeTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if( verificationCodeTxt.getText().length() > 0 && verificationCodeTxt.getText().length() < 5){

                        verificationCodeTxtLayout.setErrorEnabled(true);
                        verificationCodeTxtLayout.setHelperTextEnabled(true);
                        verificationCodeTxtLayout.setHelperText("Invalid reference code.");

                        return;
                    }

                    submitPaymentVerification();

                    break;

                case R.id.copyBtn:

                    ClipboardManager clipboard = (ClipboardManager) getSystemService(CLIPBOARD_SERVICE);
                    String getstring = copyText.getText().toString();
                    appFunctions.showMessage("Copied.");
                    break;


                default:
                    break;



            }

        }


    }
}
