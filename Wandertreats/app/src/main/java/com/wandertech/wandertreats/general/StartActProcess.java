package com.wandertech.wandertreats.general;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;

import androidx.fragment.app.Fragment;

import com.wandertech.wandertreats.utils.Utils;

public class StartActProcess {
    Context a;

    public StartActProcess(Context mContext) {
        this.a = mContext;
        Utils.hideKeyboard(mContext);
    }

    public void startAct(Class<?> cls) {
        Intent var2 = new Intent(this.a, cls);
        this.a.startActivity(var2);
    }

    public void startAct(Intent intent) {
        this.a.startActivity(intent);
    }

    public void startActWithData(Class<?> cls, Bundle bundle) {
        Intent var3 = new Intent(this.a, cls);
        var3.putExtras(bundle);
        this.a.startActivity(var3);
    }

    @SuppressLint("WrongConstant")
    public void startActWithDataNewTask(Class<?> cls, Bundle bundle) {
        Intent var3 = new Intent(this.a, cls);
        var3.putExtras(bundle);
        var3.setFlags(268435456);
        this.a.startActivity(var3);
    }
    @SuppressLint("WrongConstant")
    public void startActWithDataClearTop(Class<?> cls, Bundle bundle) {
        Intent var3 = new Intent(this.a, cls);
        var3.addFlags(67108864);
        var3.putExtras(bundle);
        this.a.startActivity(var3);
    }
    @SuppressLint("WrongConstant")
    public void startActClearTop(Class<?> cls, Bundle bundle) {
        Intent var3 = new Intent(this.a, cls);
        var3.addFlags(67108864);
        var3.putExtras(bundle);
        this.a.startActivity(var3);
    }
    @SuppressLint("WrongConstant")
    public void startActClearTop(Class<?> cls) {
        Intent var2 = new Intent(this.a, cls);
        var2.addFlags(67108864);
        this.a.startActivity(var2);
    }
    @SuppressLint("WrongConstant")
    public void startActForResult(Class<?> cls, Bundle bundle, int requestCode) {
        Intent var4 = new Intent(this.a, cls);
        var4.putExtras(bundle);
        ((Activity)this.a).startActivityForResult(var4, requestCode);
    }
    @SuppressLint("WrongConstant")
    public void startActForResult(Class<?> cls, int requestCode) {
        Intent var3 = new Intent(this.a, cls);
        ((Activity)this.a).startActivityForResult(var3, requestCode);
    }
    @SuppressLint("WrongConstant")
    public void requestOverlayPermission(int requestCode) {
        try {
            Intent var2 = new Intent("android.settings.action.MANAGE_OVERLAY_PERMISSION");
            var2.setData(Uri.parse("package:" + this.a.getApplicationInfo().packageName));
            ((Activity)this.a).startActivityForResult(var2, requestCode);
        } catch (Exception var5) {
            Intent var3 = new Intent("android.settings.APPLICATION_DETAILS_SETTINGS");
            Uri var4 = Uri.fromParts("package", this.a.getApplicationInfo().packageName, (String)null);
            var3.setData(var4);
            ((Activity)this.a).startActivityForResult(var3, requestCode);
        }

    }
    @SuppressLint("WrongConstant")
    public void startActForResult(String cls, int requestCode) {
        Intent var3 = new Intent(cls);
        ((Activity)this.a).startActivityForResult(var3, requestCode);
    }
    @SuppressLint("WrongConstant")
    public void startActForResult(Fragment fragment, Class<?> cls, int requestCode) {
        Intent var4 = new Intent(this.a, cls);
        fragment.startActivityForResult(var4, requestCode);
    }
    @SuppressLint("WrongConstant")
    public void startActForResult(Fragment fragment, Class<?> cls, int requestCode, Bundle bundle) {
        Intent var5 = new Intent(this.a, cls);
        var5.putExtras(bundle);
        fragment.startActivityForResult(var5, requestCode);
    }

    public void setOkResult() {
        Intent var1 = new Intent();
        Activity var10000 = (Activity)this.a;
        Activity var10001 = (Activity)this.a;
        var10000.setResult(-1, var1);
    }

    public void setOkResult(Bundle bn) {
        Intent var2 = new Intent();
        var2.putExtras(bn);
        Activity var10000 = (Activity)this.a;
        Activity var10001 = (Activity)this.a;
        var10000.setResult(-1, var2);
    }

    public void setOkResult(int resultCode) {
        Intent var2 = new Intent();
        ((Activity)this.a).setResult(resultCode, var2);
    }

    public void startService(Class<?> cls) {
        Intent var2 = new Intent(this.a, cls);
        this.a.startService(var2);
    }

    public Intent startForegroundService(Class<?> cls) {
        Intent var2 = new Intent(this.a, cls);
        if (Build.VERSION.SDK_INT >= 26) {
            this.a.startForegroundService(var2);
        } else {
            this.a.startService(var2);
        }

        return var2;
    }

    public boolean openURL(String url) {
        try {
            Uri.Builder var2 = new Uri.Builder();
           // CustomTabsIntent var6 = var2.build();
//            var6.intent.setPackage("com.android.chrome");
//            var6.launchUrl(this.a, Uri.parse(url));
        } catch (Exception var5) {
            try {
                Intent var3 = new Intent("android.intent.action.VIEW", Uri.parse(url));
                this.a.startActivity(var3);
            } catch (Exception var4) {
            }
        }

        return true;
    }

    public boolean openURL(String url, String packageName, String className) {
        try {
            Intent var4 = new Intent("android.intent.action.VIEW", Uri.parse(url));
            var4.setClassName(packageName, className);
            this.a.startActivity(var4);
            return true;
        } catch (Exception var5) {
            this.openURL(url);
            return false;
        }
    }
}
