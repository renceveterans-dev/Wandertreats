package com.wandertech.wandertreats.store.general;


import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.view.ViewGroup;

import com.wandertech.wandertreats.store.MyApp;

public class NetworkChangeReceiver extends BroadcastReceiver {

    @Override
    public void onReceive(Context context, Intent intent) {

        checkNetworkSettings();

    }

    private void checkNetworkSettings() {
        Activity currentActivity = MyApp.getInstance().getCurrentAct();

        if (currentActivity != null) {

        }
    }

    private void handleNetworkView(Activity activity, ViewGroup viewGroup) {
        try {
         //   OpenNoLocationView.getInstance(activity, viewGroup).configView(true);
        } catch (Exception e) {

        }
    }
}
