package com.wandertech.wandertreats.general;

import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.os.Build;

import androidx.core.app.NotificationCompat;

import com.wandertech.wandertreats.BuildConfig;
import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.utils.Constants;

public class LocalNotification {
    static Context mContext;

    private static String CHANNEL_ID = BuildConfig.APPLICATION_ID + "";
    private static NotificationManager mNotificationManager = null;

    public static void dispatchLocalNotification(Context context, String title, String message, boolean onlyInBackground) {
        mContext = context;

        if (MyApp.getInstance().getCurrentActivity() == null && mContext == null) {
            return;
        }

        continueDispatchNotification(title, message, onlyInBackground);
    }

    private static void continueDispatchNotification(String title, String message, boolean onlyInBackground) {
        Intent intent = null;

        if (GeneralFunctions.getPreviousIntent(mContext) != null) {
            intent = GeneralFunctions.getPreviousIntent(mContext);
        } else {
            intent = mContext
                    .getPackageManager()
                    .getLaunchIntentForPackage(mContext.getPackageName());

            intent.setFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT |
                    Intent.FLAG_ACTIVITY_NEW_TASK |
                    Intent.FLAG_ACTIVITY_RESET_TASK_IF_NEEDED);
        }

        PendingIntent contentIntent = PendingIntent.getActivity(mContext, 0, intent, PendingIntent.FLAG_UPDATE_CURRENT);

        NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(mContext, CHANNEL_ID)
                .setSmallIcon(R.mipmap.ic_launcher)
                // .setLargeIcon(BitmapFactory.decodeResource(mContext.getResources(), R.drawable.logo))
                .setContentTitle(title)
                .setContentText(message)
                .setAutoCancel(true)
                .setDefaults(Notification.DEFAULT_ALL)
                .setContentIntent(contentIntent)
                .setStyle(new NotificationCompat.BigTextStyle().bigText(message))
                .setPriority(NotificationCompat.PRIORITY_HIGH);

        if (mNotificationManager != null) {
            mNotificationManager.cancelAll();
            mNotificationManager = null;
        }

        // Receive Notifications in >26 version devices
        mNotificationManager = (NotificationManager) mContext.getSystemService(Context.NOTIFICATION_SERVICE);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            mBuilder.setChannelId(BuildConfig.APPLICATION_ID);
            NotificationChannel channel = new NotificationChannel(
                    CHANNEL_ID,
                    mContext.getString(R.string.app_name),
                    NotificationManager.IMPORTANCE_HIGH
            );
            if (mNotificationManager != null) {
                mNotificationManager.createNotificationChannel(channel);
            }
        }

//        if (onlyInBackground && MyApp.getInstance().isMyAppInBackGround()) {
//            mNotificationManager.notify(Constants.NOTIFICATION_ID, mBuilder.build());
//        } else if (!onlyInBackground) {
//
//        }

        mNotificationManager.notify(Constants.NOTIFICATION_ID, mBuilder.build());
    }

    public static void clearAllNotifications() {
        if (mNotificationManager != null) {
            mNotificationManager.cancelAll();
            mNotificationManager = null;
        }
    }
}
