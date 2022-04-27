package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Toast;

import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.facebook.shimmer.ShimmerFrameLayout;
import com.google.android.material.appbar.MaterialToolbar;
import com.wandertech.wandertreats.adapter.NotificationAdapter;
import com.wandertech.wandertreats.databinding.ActivityNotificationBinding;
import com.wandertech.wandertreats.databinding.ActivityProductBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.model.ParentModel;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;

import java.util.ArrayList;
import java.util.HashMap;

public class NotificationActivity extends BaseActivity implements NotificationAdapter.ItemClickList{

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private ArrayList<HashMap<String, String>> notifArr = new ArrayList<>();
    private AppCompatTextView title;
    private ActivityNotificationBinding binding;
    private AppCompatTextView titleTxt;
    private AppCompatImageView backImgView;
    private RecyclerView notificationRecyclerView;
    private NotificationAdapter notificationAdapter;
    private ShimmerFrameLayout mainContentShimmering;
    private LinearLayoutCompat notificationListArea, noDataArea;
    private MaterialToolbar materialToolbar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityNotificationBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        initView();

        titleTxt.setText("Notification");

        notificationAdapter = new NotificationAdapter(getActContext(), notifArr);
        notificationRecyclerView.setLayoutManager(new LinearLayoutManager(getActContext()));
        notificationRecyclerView.setAdapter(notificationAdapter);

        materialToolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                onBackPressed();
            }
        });

    }

    private void initView() {

        titleTxt = binding.mainToolbar.titleTxt;
        materialToolbar =binding.mainToolbar.toolbar;
        notificationRecyclerView = binding.notificationRecyclerView;
        mainContentShimmering = binding.mainContentShimmering;

        noDataArea = binding.noDataArea;
        notificationListArea = binding.notificationListArea;
    }

    public void loadData() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_NOTIFICATION");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_notifications.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                 ///Toast.makeText(getActContext(),notifArr.toString(), Toast.LENGTH_SHORT).show();

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        try{

                            notifArr = Data.getNotificationData(appFunctions.getJsonArray("data", responseString), appFunctions);


                            if(notifArr.size()>0){

                                mainContentShimmering.setVisibility(View.GONE);
                                notificationListArea.setVisibility(View.VISIBLE);
                                noDataArea.setVisibility(View.GONE);

                                notificationAdapter = new NotificationAdapter(getActContext(), notifArr);
                                notificationRecyclerView.setLayoutManager(new LinearLayoutManager(getActContext()));
                                notificationRecyclerView.setAdapter(notificationAdapter);
                                notificationAdapter.onItemClick(NotificationActivity.this);

                            }else{
                                mainContentShimmering.setVisibility(View.GONE);
                                notificationListArea.setVisibility(View.GONE);
                                noDataArea.setVisibility(View.VISIBLE);
                            }
                        }catch (Exception e){

                        }

                    }else{

                        //Toast.makeText(getActContext(),responseString, Toast.LENGTH_SHORT).show();
                        mainContentShimmering.setVisibility(View.GONE);
                        notificationListArea.setVisibility(View.GONE);
                        noDataArea.setVisibility(View.VISIBLE);
                    }

                }else{
                    mainContentShimmering.setVisibility(View.GONE);
                    notificationListArea.setVisibility(View.VISIBLE);
                    noDataArea.setVisibility(View.VISIBLE);
                }
            }
        });
        exeWebServer.execute();
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadData();
    }

    private Context getActContext() {

        return NotificationActivity.this;
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }

    @Override
    public void onItemClick(int position) {

        openNotification(position);

    }

    public void openNotification(int position) {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "OPEN_NOTIFICATION");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("notifId", notifArr.get(position).get("iNotificationId")+"");
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_notifications.php?openNotification=true", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                Bundle bn = new Bundle();
                bn.putSerializable("data", notifArr.get(position));
                new StartActProcess(getActContext()).startActWithData(NotificationDetailsActivity.class, bn);
            }
        });
        exeWebServer.execute();
    }

    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

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
