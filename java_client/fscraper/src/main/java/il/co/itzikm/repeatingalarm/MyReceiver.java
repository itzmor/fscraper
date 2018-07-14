package il.co.itzikm.repeatingalarm;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

import il.co.itzikm.common.logger.Log;
import com.facebook.AccessToken;


public    class MyReceiver extends BroadcastReceiver   {
    public static final String TAG = "MyReceiver";

    @Override
    public void onReceive(Context context, Intent intent) {
        Log.i(TAG, "Scheduler woke up on: " + WumActions.getDate());
        String notification = WumActions.get_unfriends(AccessToken.getCurrentAccessToken());

        if (!notification.isEmpty()) {
            WumActions.sendNotification(context, notification);
        }

    }
}
