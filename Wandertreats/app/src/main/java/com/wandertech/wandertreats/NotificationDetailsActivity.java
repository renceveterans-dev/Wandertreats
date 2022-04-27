package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import com.wandertech.wandertreats.adapter.MainAdapter;
import com.wandertech.wandertreats.databinding.ActivityNotificationDetailsBinding;
import com.wandertech.wandertreats.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.recyclerview.widget.LinearLayoutManager;

public class NotificationDetailsActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow;
    private ActivityNotificationDetailsBinding binding;
    private LinearLayout closeBtn;
    private AppCompatTextView titleText;
    private AppCompatTextView messageText;
    private HashMap<String, String> notifMap;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityNotificationDetailsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        titleText = binding.titleText;
        closeBtn = binding.closeBtn;
        messageText = binding.messageText;

        notifMap = (HashMap<String, String>) getIntent().getSerializableExtra("data");

        titleText.setText(notifMap.get("vTitle"));
        messageText.setText(notifMap.get("vDescription"));

        closeBtn.setOnClickListener(new setOnClickAct());

    }

    private Context getActContext() {

        return NotificationDetailsActivity.this;
    }

    public void loadNotificationDetails() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_NOTIFICATION_DETAILS");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_search.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false, appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        //appFunctions.showMessage(responseString);

                    }else{


                    }

                }

            }
        });
        exeWebServer.execute();
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){
                case R.id.closeBtn:
                    onBackPressed();
                    break;


                default:
                    break;



            }

        }


    }
}
