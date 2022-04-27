package com.wandertech.wandertreats;

import android.content.Context;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.os.Environment;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Toast;

import com.wandertech.wandertreats.databinding.ActivityMainBinding;
import com.wandertech.wandertreats.databinding.ActivityPurchaseConfirmationBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;

import org.json.JSONArray;

import java.io.File;
import java.io.FileOutputStream;

import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;

public class PurchaseConfirmationActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow;
    private ActivityPurchaseConfirmationBinding binding;
    private LinearLayoutCompat downloadBtn;
    private AppCompatButton okayBtn;
    private AppCompatTextView titleTxt, downloadTxt, headeingTxt, messageTxt, storeNameTxt, subtotalAmounTxt, totalAmounTxt, purchaseNoTxt, purchaseDateTxt;
    private String data = "", paymentType = "";
    private LinearLayoutCompat paidDetailsArea, unPaidDetailsArea;
    private AppCompatTextView toPayMessage, toPayAmounTxt;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityPurchaseConfirmationBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        data = getIntent().getStringExtra("data");
        paymentType = getIntent().getStringExtra("paymentType");

        titleTxt = binding.titleTxt;
        headeingTxt = binding. headeingTxt;
        messageTxt = binding.messageTxt;
        downloadBtn = binding.downloadBtn;
        downloadTxt = binding.downloadTxt;
        storeNameTxt = binding.storeNameTxt;
        subtotalAmounTxt = binding.subtotalAmounTxt;
        totalAmounTxt = binding.totalAmounTxt;
        purchaseNoTxt = binding.purchaseNoTxt;
        purchaseDateTxt = binding.purchaseDateTxt;
        paidDetailsArea = binding.paidDetailsArea;
        unPaidDetailsArea = binding.unPaidDetailsArea;
        toPayMessage = binding.toPayMessage;
        toPayAmounTxt = binding.toPayAmounTxt;
        okayBtn = binding.okayBtn;

        try{

            //appFunctions.showMessage(data);

            titleTxt.setText(getActContext().getResources().getString(R.string.app_name));

            JSONArray purchaseArr = appFunctions.getJsonArray("productData", data);
            String productData = appFunctions.getJsonValue(purchaseArr, 0).toString();

            JSONArray merchantArr = appFunctions.getJsonArray("merchantData", data);
            String merchantData = appFunctions.getJsonValue(merchantArr, 0).toString();

            headeingTxt.setText("Successfuly Confirmed");
            downloadTxt.setText("Download");

            storeNameTxt.setText(appFunctions.getJsonValue("vStoreName", data));
            purchaseDateTxt.setText(appFunctions.getJsonValue("tPurchaseRequestDate", data));
            purchaseNoTxt.setText(appFunctions.getJsonValue("vPurchaseNo", data));

            if(paymentType.equalsIgnoreCase("PAY_AT THE_STORE")){
                paidDetailsArea.setVisibility(View.GONE);
                unPaidDetailsArea.setVisibility(View.VISIBLE);
                toPayMessage.setText("Total amount to be paid on the store is");
                toPayAmounTxt.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fTotalGenerateFare", data)));
            }else{
                paidDetailsArea.setVisibility(View.VISIBLE);
                unPaidDetailsArea.setVisibility(View.GONE);
                subtotalAmounTxt.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fSubTotal", data)));
                totalAmounTxt.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fTotalGenerateFare", data)));
            }

        }catch (Exception e){
            appFunctions.showMessage(e.toString());
        }

        downloadBtn.setOnClickListener(new setOnClickAct());
        okayBtn.setOnClickListener(new setOnClickAct());
    }


    private Context getActContext() {

        return PurchaseConfirmationActivity.this;
    }

    @Override
    public void onBackPressed() {

    }

    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){


                case R.id.okayBtn:

                    Bundle bn =  new Bundle();
                    bn.putString("isStart", "Treats");
                    new StartActProcess(getActContext()).startActWithData(MainActivity.class,bn);
                    finish();

                    break;

                case R.id.downloadBtn:
                    View rootView = getWindow().getDecorView().findViewById(android.R.id.content);

                    store(getScreenShot(rootView), appFunctions.getJsonValue("vPurchaseNo", data));

                    break;

                default:
                    break;



            }

        }


    }
    public static Bitmap getScreenShot(View view) {
        View screenView = view.getRootView();
        screenView.setDrawingCacheEnabled(true);
        Bitmap bitmap = Bitmap.createBitmap(screenView.getDrawingCache());
        screenView.setDrawingCacheEnabled(false);
        return bitmap;
    }

    public void store(Bitmap bm, String fileName){
        final String dirPath = Environment.getExternalStorageDirectory().getAbsolutePath() + "/Screenshots";
        File dir = new File(dirPath);
        if(!dir.exists())
            dir.mkdirs();
        File file = new File(dirPath, fileName);
        try {
            FileOutputStream fOut = new FileOutputStream(file);
            bm.compress(Bitmap.CompressFormat.PNG, 85, fOut);
            fOut.flush();
            fOut.close();

            appFunctions.showMessage("Successfuly saved.");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
