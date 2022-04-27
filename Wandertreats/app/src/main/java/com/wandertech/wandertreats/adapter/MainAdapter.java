package com.wandertech.wandertreats.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.general.Data;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.model.ItemModel;
import com.wandertech.wandertreats.model.ParentModel;

import java.util.ArrayList;
import java.util.HashMap;

public class MainAdapter extends RecyclerView.Adapter<MainAdapter.ItemViewHolder> {
    private ArrayList<ParentModel> parentArrayList;
    private ArrayList<HashMap<String, String>> resArrList;
    private ArrayList<HashMap<String, String>> flashList;
    private ArrayList<HashMap<String, String>> bulkList;
    public static final int TYPE_HORIZONTAL = 1;
    public static final int TYPE_VERTICAL = 2;
    private Context mContext;
    private GeneralFunctions appFunctions;

    public static class ItemViewHolder extends RecyclerView.ViewHolder {

        public RecyclerView itemRecyclerView;
        public AppCompatTextView mainTitle;
        public LinearLayoutCompat layouArea;
        public ItemViewHolder(View itemView) {
            super(itemView);

            mainTitle = itemView.findViewById(R.id.mainTitle);
            itemRecyclerView = itemView.findViewById(R.id.itemRecyclerView);
            layouArea = itemView.findViewById(R.id.layouArea);
        }
    }

    public MainAdapter(ArrayList<ParentModel> exampleList, Context context, GeneralFunctions appFunctions) {

        this.parentArrayList = exampleList;
        this.resArrList = resArrList;
        this.mContext = context;
        this.appFunctions = appFunctions;

    }

    @Override
    public ItemViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main, parent, false);
        return new ItemViewHolder(view);

    }

    @Override
    public int getItemViewType(int position) {

        if(parentArrayList.get(position).getMainType().equalsIgnoreCase("HORIZONTAL")){
            return TYPE_HORIZONTAL;
        }else{
            return TYPE_VERTICAL;
        }


    }

    @Override
    public int getItemCount() {
        return parentArrayList.size();
    }

    @Override
    public void onBindViewHolder(ItemViewHolder holder, @SuppressLint("RecyclerView") int position) {

        ParentModel currentItem = parentArrayList.get(position);

        if(currentItem.getMainType().equalsIgnoreCase("HORIZONTAL")){
            RecyclerView.LayoutManager layoutManager = new LinearLayoutManager(mContext, LinearLayoutManager.HORIZONTAL, false);
            holder.itemRecyclerView.setLayoutManager(layoutManager);
            holder.itemRecyclerView.setHasFixedSize(true);
            holder.mainTitle.setText(currentItem.getMainTitle());

            ArrayList<ItemModel> arrayList = new ArrayList<>();
            arrayList = Data.getProductData(appFunctions.getJsonArray(currentItem.getMainProducts()), appFunctions);
            MainItemAdapter mainItemAdapter = new MainItemAdapter(arrayList,holder.itemRecyclerView.getContext());
            holder.itemRecyclerView.setAdapter(mainItemAdapter);

        }else{
            RecyclerView.LayoutManager layoutManager = new LinearLayoutManager(mContext, LinearLayoutManager.VERTICAL, false);
            holder.itemRecyclerView.setLayoutManager(layoutManager);
            holder.itemRecyclerView.setHasFixedSize(true);
            holder.mainTitle.setText(currentItem.getMainTitle());

            ArrayList<ItemModel> arrayList = new ArrayList<>();
            arrayList = Data.getProductData(appFunctions.getJsonArray(currentItem.getMainProducts()), appFunctions);
            MainItemListAdapter mainItemLisAdapter = new MainItemListAdapter(arrayList,holder.itemRecyclerView.getContext());
            holder.itemRecyclerView.setAdapter(mainItemLisAdapter);
        }



    }




}