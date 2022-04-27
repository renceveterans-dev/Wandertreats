package com.wandertech.wandertreats.general;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.google.android.material.bottomsheet.BottomSheetDialogFragment;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.GCashPaymentActivity;
import com.wandertech.wandertreats.PurchaseConfirmationActivity;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.adapter.MerchantAdapter;
import com.wandertech.wandertreats.adapter.RadioSelctionAdapter;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.fragment.app.DialogFragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

public class PaymentBottomSheetFragment extends BottomSheetDialogFragment implements RadioSelctionAdapter.ItemOnClickListener{
    public int  headerHeight = 0;
    public GeneralFunctions appFunctions;
    public Context mContext;
    public ArrayList<HashMap<String, String>> dataList;
    public ArrayList<HashMap<String, String>> mainStores;
    public ItemOnClickListener itemOnClickListener;
    public int position;
    public String paymentData = "";
    public JSONArray paymentMethodArr = new JSONArray();
    public RecyclerView paymentRecyclerView;
    public RadioSelctionAdapter radioSelctionAdapter;
    public AppCompatButton prodeedBtn;
    public int selected = 111;
    public boolean hasSelected = false;
    public HashMap<String, String> parameters = new HashMap<String, String>();

    public PaymentBottomSheetFragment(Context mContext,  HashMap<String, String> parameters, GeneralFunctions appFunctions) {
        // Required empty public constructor

        this.appFunctions = appFunctions;
        this.mContext = mContext;
        this.parameters = parameters;

    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        setStyle(DialogFragment.STYLE_NO_FRAME, R.style.AppBottomSheetDialogTheme);
        View bottomSheetView = inflater.inflate(R.layout.dialog_payment, container, false);
        // Inflate the layout for this fragment

        paymentData = appFunctions.retrieveValue(Utils.APP_GENERAL_DATA);
        paymentMethodArr = appFunctions.getJsonArray("paymentMethods", paymentData);

        try{

            paymentRecyclerView = bottomSheetView.findViewById(R.id.paymentRecyclerView);
            prodeedBtn = bottomSheetView.findViewById(R.id.prodeedBtn);
            dataList = Data.getPaymentData(paymentMethodArr, appFunctions);
           // appFunctions.showMessage( dataList.toString());
            radioSelctionAdapter = new RadioSelctionAdapter(mContext, dataList);
            paymentRecyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
            paymentRecyclerView.setAdapter(radioSelctionAdapter);
            radioSelctionAdapter.setOnItemClick(this::setOnItemClick);

            prodeedBtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {

                    if(hasSelected){
                        payNow();
                    }else{
                        appFunctions.showMessage("Please select payment method.");
                    }

                }
            });

        }catch (Exception e){
            appFunctions.showMessage(e.toString());
        }


        return bottomSheetView;
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

        radioSelctionAdapter = new RadioSelctionAdapter(mContext, tempData);
        paymentRecyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
        paymentRecyclerView.setAdapter(radioSelctionAdapter);
        radioSelctionAdapter.setOnItemClick(this::setOnItemClick);

    }

    public void payNow() {

        try{

            ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(mContext, parameters, "api_purchase_now.php", true);
            exeWebServer.setLoaderConfig(mContext, true,appFunctions);
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
                            new StartActProcess(mContext).startActWithData(GCashPaymentActivity.class,bn);

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


    public interface ItemOnClickListener {
        void setOnItemClick(String paymentType);
    }

    public void setOnItemClick(ItemOnClickListener onItemClick) {
        this.itemOnClickListener = onItemClick;
    }


}