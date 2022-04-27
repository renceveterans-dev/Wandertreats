package com.wandertech.wandertreats.general;

import android.annotation.SuppressLint;
import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

public class InternetConnection {
    private final Context a;

    public InternetConnection(Context context) {
        this.a = context;
        this.a(context);
    }

    public boolean isNetworkConnected() {
        @SuppressLint("WrongConstant") ConnectivityManager var1 = (ConnectivityManager)this.a.getSystemService("connectivity");
        NetworkInfo var2 = var1.getNetworkInfo(1);
        boolean var3 = var2 != null && var2.isConnected();
        NetworkInfo var4 = var1.getNetworkInfo(0);
        boolean var5 = var4 != null && var4.isConnected();
        return var3 || var5;
    }

    public boolean check_int() {
        @SuppressLint("WrongConstant") ConnectivityManager var1 = (ConnectivityManager)this.a.getSystemService("connectivity");
        NetworkInfo var2 = var1.getActiveNetworkInfo();
        if (var2 == null) {
            return false;
        } else if (!var2.isConnected()) {
            return false;
        } else {
            return var2.isAvailable();
        }
    }

    private void a(Context var1) {
        new InfoProvider(var1);
    }
}
