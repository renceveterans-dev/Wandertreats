package com.wandertech.wandertreats;

import android.content.ContentResolver;
import android.content.ContentValues;
import android.content.Context;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.provider.MediaStore;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.Toast;

import com.vipul.hp_hp.library.Layout_to_Image;
import com.wandertech.wandertreats.databinding.ActivityMainBinding;
import com.wandertech.wandertreats.databinding.ActivityPurchaseConfirmationBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.ImagePreviewDialog;
import com.wandertech.wandertreats.general.LayoutToImage;
import com.wandertech.wandertreats.general.StartActProcess;

import org.json.JSONArray;

import java.io.Console;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;

import androidx.annotation.NonNull;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;

public class PurchaseConfirmationActivity extends BaseActivity {

    private static String IMAGES_FOLDER_NAME = "Wandertreats";
    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow;
    private ActivityPurchaseConfirmationBinding binding;
    LinearLayout mainContent;
    private LinearLayoutCompat downloadBtn, receiptArea;
    private AppCompatButton okayBtn;
    private AppCompatTextView titleTxt, downloadTxt, headeingTxt, messageTxt, storeNameTxt, subtotalAmounTxt, totalAmounTxt, purchaseNoTxt, purchaseDateTxt;
    private String data = "", paymentType = "";
    private LinearLayoutCompat paidDetailsArea, unPaidDetailsArea;
    private AppCompatTextView toPayMessage, toPayAmounTxt;

    private AppCompatTextView downloadTxt2, headeingTxt2, messageTxt2, storeNameTxt2, subtotalAmounTxt2, totalAmounTxt2, purchaseNoTxt2, purchaseDateTxt2;
    private LinearLayoutCompat paidDetailsArea2, unPaidDetailsArea2;
    private AppCompatTextView toPayMessage2, toPayAmounTxt2;

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
        mainContent = binding.mainContent;
        receiptArea = binding.receiptArea;
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

        headeingTxt2 = binding. headeingTxt2;
        messageTxt2 = binding.messageTxt2;
        storeNameTxt2 = binding.storeNameTxt2;
        subtotalAmounTxt2 = binding.subtotalAmounTxt2;
        totalAmounTxt2 = binding.totalAmounTxt2;
        purchaseNoTxt2 = binding.purchaseNoTxt2;
        purchaseDateTxt2 = binding.purchaseDateTxt2;
        paidDetailsArea2 = binding.paidDetailsArea2;
        unPaidDetailsArea2 = binding.unPaidDetailsArea2;
        toPayMessage2 = binding.toPayMessage2;
        toPayAmounTxt2 = binding.toPayAmounTxt2;

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

            headeingTxt2.setText("Successfuly Confirmed");

            storeNameTxt2.setText(appFunctions.getJsonValue("vStoreName", data));
            purchaseDateTxt2.setText(appFunctions.getJsonValue("tPurchaseRequestDate", data));
            purchaseNoTxt2.setText(appFunctions.getJsonValue("vPurchaseNo", data));

            if(paymentType.equalsIgnoreCase("PAY_AT THE_STORE")){
                paidDetailsArea2.setVisibility(View.GONE);
                unPaidDetailsArea2.setVisibility(View.VISIBLE);
                toPayMessage2.setText("Total amount to be paid on the store is");
                toPayAmounTxt2.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fTotalGenerateFare", data)));
            }else{
                paidDetailsArea2.setVisibility(View.VISIBLE);
                unPaidDetailsArea2.setVisibility(View.GONE);
                subtotalAmounTxt2.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fSubTotal", data)));
                totalAmounTxt2.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fTotalGenerateFare", data)));
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

                    try{
                        //View rootView = getWindow().getDecorView().findViewById(android.R.id.content);
                        downloadReceipt();
                    }catch (Exception e){
                        appFunctions.showMessage(e.toString());
                    }

                   // store(getScreenShot(rootView), appFunctions.getJsonValue("vPurchaseNo", data));

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

    public void downloadReceipt() {

        LayoutToImage layoutToImage;  //Create Object of Layout_to_Image Class
        //RelativeLayout relativeLayout;   //Define Any Layout

        Bitmap bitmap;                  //Bitmap for holding Image of layout

        //provide layout with its id in Xml

       // relativeLayout=(RelativeLayout)findViewById(R.id.container);

        //initialise layout_to_image object with its parent class and pass parameters as (<Current Activity>,<layout object>)

        try {
            //receiptArea.setVisibility(View.VISIBLE);
            layoutToImage = new LayoutToImage(PurchaseConfirmationActivity.this, receiptArea);
            //receiptArea.setVisibility(View.GONE);
            //now call the main working function ;) and hold the returned image in bitmap

            bitmap = layoutToImage.convertLayout();
            try {
                saveImage( bitmap, appFunctions.getJsonValue("vPurchaseNo", data));
            } catch (IOException e) {
                e.printStackTrace();
                appFunctions.showMessage("3"+e.toString());
                System.out.println("3"+e.toString());
            }
        } catch (Exception e) {
            e.printStackTrace();
            appFunctions.showMessage("4"+e.toString());
            System.out.println("4"+e.toString());
        }



        appFunctions.showMessage("Successfully saved to gallery.");

//        try{
//            ImagePreviewDialog imagePreviewDialog = new ImagePreviewDialog(getActContext());
//            imagePreviewDialog .createPreview(bitmap);
//            imagePreviewDialog.show();
//
//        }catch (Exception e){
//            appFunctions.showMessage(e.toString());
//        };




    }

    private void saveImage(Bitmap bitmap, @NonNull String name) throws IOException {
        boolean saved;
        OutputStream fos;

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            ContentResolver resolver = getActContext().getContentResolver();
            ContentValues contentValues = new ContentValues();
            contentValues.put(MediaStore.MediaColumns.DISPLAY_NAME, name);
            contentValues.put(MediaStore.MediaColumns.MIME_TYPE, "image/png");
            contentValues.put(MediaStore.MediaColumns.RELATIVE_PATH, "DCIM/" + IMAGES_FOLDER_NAME);
            Uri imageUri = resolver.insert(MediaStore.Images.Media.EXTERNAL_CONTENT_URI, contentValues);
            fos = resolver.openOutputStream(imageUri);
        } else {
            String imagesDir = Environment.getExternalStoragePublicDirectory(
                    Environment.DIRECTORY_DCIM).toString() + File.separator + IMAGES_FOLDER_NAME;

            File file = new File(imagesDir);

            if (!file.exists()) {
                file.mkdir();
            }

            File image = new File(imagesDir, name + ".png");
            fos = new FileOutputStream(image);

        }

        saved = bitmap.compress(Bitmap.CompressFormat.PNG, 100, fos);
        fos.flush();
        fos.close();


//        ImagePreviewDialog imagePreviewDialog = new ImagePreviewDialog(getActContext());
//        imagePreviewDialog .createPreview( saved);
//        imagePreviewDialog.show();
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
            appFunctions.showMessage(e.getMessage());
        }
    }
}
