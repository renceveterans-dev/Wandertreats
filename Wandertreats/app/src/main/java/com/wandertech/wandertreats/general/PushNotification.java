package com.wandertech.wandertreats.general;

import android.app.Activity;
import android.app.NotificationManager;
import android.content.Context;
import android.os.Handler;
import android.os.Looper;
import android.widget.Toast;

import com.wandertech.wandertreats.MainActivity;
import com.wandertech.wandertreats.MyApp;

public class PushNotification {


    private FragmentRefreshListener fragmentRefreshListener;
    private FragmentNotificationListener fragmentNotificationListener;
    static Context mContext;
    static GeneralFunctions appFunctions;
    private static NotificationManager mNotificationManager = null;

    public static void fireNotification(Context context, String title, String message, boolean onlyInBackground) {
        mContext = context;
        setUiView(title, message);
    }

    public static void setUiView(String activity, String message){
        Handler handler = new Handler(Looper.getMainLooper());
        handler.post(new Runnable() {
            public void run() {

                try {

                    Activity currentActivity = ((MyApp) mContext.getApplicationContext()).getCurrentActivity();
                    if(currentActivity instanceof MainActivity){
                        Toast.makeText(mContext.getApplicationContext(), "aaoaaoas", Toast.LENGTH_SHORT).show();
//                        int position = ((HomeActivity) currentActivity).viewPager.getCurrentItem();
                    }
                    //else  if(currentActivity instanceof TrackOrderActivity){
                        //((TrackOrderActivity) currentActivity).showPopNotifications(activity, message);
                    //}else  if(currentActivity instanceof ActiveBookingActivity){
                       // ((ActiveBookingActivity) currentActivity).setAlertNotification(activity, message);
                    //}

                }catch (Exception e){
                    Toast.makeText(mContext,  e.toString(), Toast.LENGTH_SHORT).show();
                }

            }
        });
    }

    public FragmentRefreshListener getFragmentRefreshListener() {
        return fragmentRefreshListener;
    }

    public void setFragmentRefreshListener(FragmentRefreshListener fragmentRefreshListener) {
        this.fragmentRefreshListener = fragmentRefreshListener;
    }

    public interface FragmentRefreshListener{
        void onRefresh(Boolean edit);
    }

    public interface FragmentNotificationListener{
        void onRefresh(Boolean edit);
    }

    public FragmentNotificationListener getNotificationListener() {
        return fragmentNotificationListener;
    }

    public void setFragmentNotificationListener(FragmentNotificationListener fragmentNotificationListener) {
        this.fragmentNotificationListener = fragmentNotificationListener;
    }


}



