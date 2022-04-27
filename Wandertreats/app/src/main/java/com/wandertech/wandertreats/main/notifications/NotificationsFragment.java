package com.wandertech.wandertreats.main.notifications;

import android.content.Context;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProvider;

import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.databinding.FragmentNotificationsBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.model.ParentModel;
import com.wandertech.wandertreats.utils.Utils;

import java.util.ArrayList;
import java.util.HashMap;

public class NotificationsFragment extends Fragment {

    private NotificationsViewModel notificationsViewModel;
    private FragmentNotificationsBinding binding;
    private ArrayList<ParentModel> notifArr = new ArrayList<>();
    private GeneralFunctions appFunctions;
    private ViewGroup mContainer;

    public View onCreateView(@NonNull LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        appFunctions = MyApp.getInstance().getGeneralFun(container.getContext());
        notificationsViewModel = new ViewModelProvider(this).get(NotificationsViewModel.class);
        mContainer = container;
        binding = FragmentNotificationsBinding.inflate(inflater, container, false);
        View root = binding.getRoot();

        final TextView textView = binding.textNotifications;
        notificationsViewModel.getText().observe(getViewLifecycleOwner(), new Observer<String>() {
            @Override
            public void onChanged(@Nullable String s) {
                textView.setText(s);
            }
        });

        loadNotification();


        return root;
    }

    public void loadNotification() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_NOTIFICATION_DETAILS");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_notifications.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false, appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        appFunctions.showMessage(responseString);

                    }else{

                    }

                }

            }
        });

        exeWebServer.execute();
    }

    public Context getActContext(){
        return  mContainer.getContext();
    }


    @Override
    public void onDestroyView() {
        super.onDestroyView();
        binding = null;
    }
}