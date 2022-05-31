package com.wandertech.wandertreats.main.feed;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProvider;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.facebook.shimmer.ShimmerFrameLayout;
import com.wandertech.wandertreats.MainActivity;
import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.adapter.FeedPostAdapter;
import com.wandertech.wandertreats.databinding.FragmentFeedBinding;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.ImagePreviewDialog;
import com.wandertech.wandertreats.utils.Utils;

import java.util.ArrayList;
import java.util.HashMap;

public class FeedFragment extends Fragment implements FeedPostAdapter.setOnClickList {

    private FeedViewModel feedViewModel;
    private FragmentFeedBinding binding;
    private ShimmerFrameLayout loaderShimmer;
    private RecyclerView feedRecyclerList;
    private ArrayList<HashMap<String, String>> feedsArr = new ArrayList<>();
    private GeneralFunctions appFunctions;
    private FeedPostAdapter feedPostAdapter;
    private LinearLayoutCompat feedRRecyclerListArea, noDataArea;
    private AppCompatImageView notificationBtn;

    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

       //
        appFunctions = MyApp.getInstance().getGeneralFun(container.getContext());
//        try {
//            ((MainActivity) getActivity()).  getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
//            appFunctions.setWindowFlag((Activity) getActivity(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);
//            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
//                ((MainActivity) getActivity()).  getWindow().setStatusBarColor(getResources().getColor(R.color.gray,  ((MainActivity) getActivity()).getTheme()));
//            } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
//                ((MainActivity) getActivity()). getWindow().setStatusBarColor(getResources().getColor(R.color.gray));
//            }
//        }catch (Exception e){
//            appFunctions.showMessage(e.toString());
//        }

        binding = FragmentFeedBinding.inflate(inflater, container, false);

        View root = binding.getRoot();

        if(getActivity() != null && isAdded()) {
            loaderShimmer = binding.loaderShimmer;
            feedRecyclerList = binding.feedRecyclerList;
            noDataArea = binding.noDataArea;
            feedRRecyclerListArea= binding.feedRRecyclerListArea;


            loadData();

        }




        return root;
    }


    public Context getActContext(){
        return  getActivity();
    }


    public void loadData() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LOAD_NOTIFICATION");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_load_feeds.php", true);
        exeWebServer.setLoaderConfig(getActContext(), false,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {



                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){

                        feedsArr = Data.getFeedsData(appFunctions.getJsonArray("data", responseString), appFunctions);
                        //Toast.makeText(getActContext(),feedsArr.toString(), Toast.LENGTH_SHORT).show();
                        if(feedsArr.size()>0){
                            loaderShimmer.setVisibility(View.GONE);
                            feedRRecyclerListArea.setVisibility(View.VISIBLE);
                            noDataArea.setVisibility(View.GONE);

                            feedPostAdapter = new FeedPostAdapter(getActivity(), feedsArr);
                            feedRecyclerList.setLayoutManager(new LinearLayoutManager(getActContext()));
                            feedPostAdapter.itemOnClick(FeedFragment.this);
                            feedRecyclerList.setAdapter(feedPostAdapter);
                        }else{
                            loaderShimmer.setVisibility(View.GONE);
                            feedRRecyclerListArea.setVisibility(View.GONE);
                            noDataArea.setVisibility(View.VISIBLE);
                        }

                    }else{

                        //Toast.makeText(getActContext(),responseString, Toast.LENGTH_SHORT).show();
                        loaderShimmer.setVisibility(View.GONE);
                        feedRRecyclerListArea.setVisibility(View.GONE);
                        noDataArea.setVisibility(View.VISIBLE);
                    }

                }else{
                    loaderShimmer.setVisibility(View.GONE);
                    feedRRecyclerListArea.setVisibility(View.VISIBLE);
                    noDataArea.setVisibility(View.VISIBLE);
                }
            }
        });
        exeWebServer.execute();
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        binding = null;
    }

    @Override
    public void itemOnClick(int position) {

        //appFunctions.showMessage("sasas");

        if(feedsArr.get(position).get("eUrlType").equalsIgnoreCase("External")){
            try{
                String url = feedsArr.get(position).get("vUrl");
                Intent i = new Intent(Intent.ACTION_VIEW);
                i.setData(Uri.parse(url));
                getActContext().startActivity(i);
            }catch (Exception e){
                Toast.makeText(getActContext(), ""+e.toString(), Toast.LENGTH_SHORT).show();
            }
        }else{

            try{
                ImagePreviewDialog imagePreviewDialog = new ImagePreviewDialog(getActivity());
                imagePreviewDialog .createPreview(feedsArr.get(position).get("vImage"));
                imagePreviewDialog.show();

            }catch (Exception e){
                appFunctions.showMessage(e.toString());
            }

        }

    }
}