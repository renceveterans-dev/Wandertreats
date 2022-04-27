package com.wandertech.wandertreats;

import android.app.Activity;
import android.app.Application;
import android.app.NotificationManager;
import android.content.Context;
import android.content.pm.ActivityInfo;
import android.os.Bundle;
import android.view.WindowManager;

import androidx.multidex.MultiDex;

import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.GpsReceiver;
import com.wandertech.wandertreats.general.NetworkChangeReceiver;

import java.util.logging.Logger;

public class MyApp extends Application {

    private static MyApp mMyApp;

    GeneralFunctions generalFun;
    boolean isAppInBackground = true;

    private GpsReceiver mGpsReceiver;
    private NetworkChangeReceiver mNetWorkReceiver = null;

    private Activity currentAct = null;


    public static synchronized MyApp getInstance() {
        return mMyApp;
    }

    @Override
    public void onCreate() {
        super.onCreate();

        mMyApp = (MyApp) this.getApplicationContext();
        generalFun = new GeneralFunctions(this);

        setScreenOrientation();

    }


    public void setScreenOrientation() {
        registerActivityLifecycleCallbacks(new ActivityLifecycleCallbacks() {

            @Override
            public void onActivityCreated(Activity activity, Bundle savedInstanceState) {
                // new activity created; force its orientation to portrait
                activity.setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
                activity.setTitle(getResources().getString(R.string.app_name));
                setCurrentAct(activity);
                activity.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

            }

            @Override
            public void onActivityStarted(Activity activity) {

            }

            @Override
            public void onActivityResumed(Activity activity) {
                setCurrentAct(activity);
                isAppInBackground = false;
            }

            @Override
            public void onActivityPaused(Activity activity) {
                isAppInBackground = true;

                // Toast.makeText(getCurrentAct(), "Background", Toast.LENGTH_SHORT).show();
            }

            @Override
            public void onActivityStopped(Activity activity) {

            }

            @Override
            public void onActivitySaveInstanceState(Activity activity, Bundle bundle) {
                /*Called to retrieve per-instance state from an activity before being killed so that the state can be restored in onCreate(Bundle) or onRestoreInstanceState(Bundle) (the Bundle populated by this method will be passed to both).*/

            }

            @Override
            public void onActivityDestroyed(Activity activity) {
            }
        });
    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(base);
        MultiDex.install(this);
    }


    @Override
    public void onLowMemory() {
        super.onLowMemory();
    }

    @Override
    public void onTrimMemory(int level) {
        super.onTrimMemory(level);

    }


    @Override
    public void onTerminate() {
        super.onTerminate();

        NotificationManager nMgr = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        nMgr.cancelAll();


    }

    public Activity getCurrentAct() {
        return currentAct;
    }

    private void setCurrentAct(Activity currentAct) {
        this.currentAct = currentAct;

    }

    public GeneralFunctions getGeneralFun(Context mContext) {
        return new GeneralFunctions(mContext);
    }

    public boolean isMyAppInBackGround() {
        return this.isAppInBackground;
    }

    public Activity getCurrentActivity () {
        return currentAct  ;
    }
}
