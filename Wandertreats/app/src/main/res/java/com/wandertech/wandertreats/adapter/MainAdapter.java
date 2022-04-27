package com.wandertech.wandertreats.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.model.ItemModel;
import com.wandertech.wandertreats.model.ParentModel;

import java.util.ArrayList;
import java.util.HashMap;

public class MainAdapter extends RecyclerView.Adapter<MainAdapter.ItemViewHolder> {
    private ArrayList<ParentModel> parentArrayList;
    ArrayList<HashMap<String, String>> resArrList;
    ArrayList<HashMap<String, String>> flashList;
    ArrayList<HashMap<String, String>> bulkList;
    public Context mContext;

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

    public MainAdapter(ArrayList<ParentModel> exampleList, Context context) {

        this.parentArrayList = exampleList;
        this.resArrList = resArrList;
        this.mContext = context;


    }

    @Override
    public ItemViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main, parent, false);
        return new ItemViewHolder(view);
    }

    @Override
    public int getItemCount() {
        return parentArrayList.size();
    }

    @Override
    public void onBindViewHolder(ItemViewHolder holder, @SuppressLint("RecyclerView") int position) {

        ParentModel currentItem = parentArrayList.get(position);
        RecyclerView.LayoutManager layoutManager = new LinearLayoutManager(mContext, LinearLayoutManager.HORIZONTAL, false);
        holder.itemRecyclerView.setLayoutManager(layoutManager);
        holder.itemRecyclerView.setHasFixedSize(true);
        ArrayList<ItemModel> arrayList = new ArrayList<>();

//        // added the first child row
//
//
//            arrayList.add(new ItemModel("hahah", "haha", "haha"));
//            arrayList.add(new ItemModel("hahah", "haha", "haha"));
//            arrayList.add(new ItemModel("hahah", "haha", "haha"));
//            arrayList.add(new ItemModel("hahah", "haha", "haha"));
//            arrayList.add(new ItemModel("hahah", "haha", "haha"));
//




        MainItemAdapter mainItemAdapter = new MainItemAdapter(arrayList,holder.itemRecyclerView.getContext());
        holder.itemRecyclerView.setAdapter(mainItemAdapter);
    }




}