package com.wandertech.wandertreats.store;

import android.Manifest;
import android.content.Context;
import android.content.pm.PackageManager;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import com.budiyev.android.codescanner.CodeScanner;
import com.budiyev.android.codescanner.CodeScannerView;
import com.budiyev.android.codescanner.DecodeCallback;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.zxing.Result;
import com.wandertech.wandertreats.store.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.utils.Utils;

import java.util.HashMap;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.core.app.ActivityCompat;

import static com.wandertech.wandertreats.store.utils.Utils.RC_CLAIM_PRODUCT;
import static com.wandertech.wandertreats.store.utils.Utils.RC_FIND_ITEM;
import static com.wandertech.wandertreats.store.utils.Utils.RC_FIND_STORE;
import static com.wandertech.wandertreats.store.utils.Utils.RC_PERMISSION;
import static com.wandertech.wandertreats.store.utils.Utils.RC_USE_VOUCHER;

public class ScanActivity extends AppCompatActivity {

    private boolean mPermissionGranted;
    public int RC_MODE = 0;
    private CodeScanner mCodeScanner;
    private String purchasedData = "";
    private GeneralFunctions appFunctions;

    private MaterialToolbar toolbar;
    private CodeScannerView scannerView;
    private AppCompatTextView titleTxt;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scan);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        RC_MODE = getIntent().getIntExtra("SCAN_MODE", 0);
        purchasedData = getIntent().getStringExtra("data");

        initView();

        setLabel();

        setPermissions();

        setCodeScanner();
    }

    private void setPermissions() {

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (checkSelfPermission(Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
                mPermissionGranted = false;
                requestPermissions(new String[] {Manifest.permission.CAMERA}, RC_PERMISSION);
            } else {
                mPermissionGranted = true;
            }
        } else {
            mPermissionGranted = true;
        }
    }

    private void setLabel() {

        titleTxt.setText("Scan");
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });
    }

    private void setCodeScanner() {

        mCodeScanner = new CodeScanner(this,  scannerView);
        mCodeScanner.setDecodeCallback(result -> runOnUiThread(() -> {
            handleResult(result);
        }));
        mCodeScanner.setErrorCallback(error -> runOnUiThread(
                () -> Toast.makeText(this, "Error", Toast.LENGTH_LONG).show()));

    }

    private void initView() {

        toolbar = findViewById(R.id.toolbar);
        titleTxt = findViewById(R.id.titleTxt);
        scannerView  = findViewById(R.id.scanner);
    }

    public void handleResult(Result result){

        appFunctions.showMessage(result.getText());
//
//        Bundle bn = new Bundle();
//
//        bn.putString("result", result.getText());
//        bn.putString("data", purchasedData);
//        new StartActProcess(getActContext()).startActWithData(ClaimActivity.class, bn);

//
//
//        if( RC_MODE == RC_CLAIM_PRODUCT){
//            bn.putInt("claimType", RC_MODE);
//            bn.putString("result", result.getText());
//            bn.putString("data", purchasedData);
//            new StartActProcess(getActContext()).startActWithData(ClaimActivity.class, bn);
//        }else if( RC_MODE == RC_FIND_ITEM){
//            bn.putInt("claimType", RC_MODE);
//            bn.putString("result", result.getText());
//            bn.putString("data", purchasedData);
//            new StartActProcess(getActContext()).startActWithData(ClaimActivity.class, bn);
//        }else if( RC_MODE == RC_FIND_STORE){
//            bn.putInt("claimType", RC_MODE);
//            bn.putString("result", result.getText());
//            bn.putString("data", purchasedData);
//            new StartActProcess(getActContext()).startActWithData(ClaimActivity.class, bn);
//        }else if( RC_MODE == RC_USE_VOUCHER){
//            bn.putInt("claimType", RC_MODE);
//
//        }

       claimItem( result.getText());

    }


    public void claimItem(String qrcode) {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "CLAIM_PRODUCT");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("vPurchaseNo", appFunctions.getJsonValue("vPurchaseNo", purchasedData));
        parameters.put("iPurchaseId", appFunctions.getJsonValue("iPurchaseId", purchasedData));
        parameters.put("storeId", appFunctions.getJsonValue("iMerchantId", purchasedData));
        parameters.put("qrCode", qrcode);
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_claim_voucher.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                //Toast.makeText(getActContext(),responseString, Toast.LENGTH_SHORT).show()
                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        Bundle bn = new Bundle();
                        bn.putInt("SCAN_MODE", RC_CLAIM_PRODUCT);
                        bn.putString("data", purchasedData);
                        new StartActProcess(getActContext()).startActWithData(ClaimActivity.class, bn);
                        finish();


                    }else{

                    }

                }

                //
            }
        });
        exeWebServer.execute();
    }


    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == RC_PERMISSION) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                mPermissionGranted = true;
                mCodeScanner.startPreview();
            } else {
                mPermissionGranted = false;
            }
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        if (mPermissionGranted) {
            mCodeScanner.startPreview();
        }
    }

    @Override
    protected void onPause() {
        mCodeScanner.releaseResources();
        super.onPause();
    }

    public Context getActContext(){
        return ScanActivity.this;
    }
}
