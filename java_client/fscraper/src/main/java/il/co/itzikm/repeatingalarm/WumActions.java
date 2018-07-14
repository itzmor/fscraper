package il.co.itzikm.repeatingalarm;

import android.app.NotificationManager;
import android.content.Context;
import android.support.v4.app.NotificationCompat;

import il.co.itzikm.common.logger.Log;
import com.facebook.AccessToken;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

import static il.co.itzikm.repeatingalarm.MainActivity.TAG;

public class WumActions   {

    public static String get_unfriends(AccessToken accessToken)
    {
        WumConnectWithWs wumConnectWithWs = new WumConnectWithWs();
        wumConnectWithWs.run_wus_action(WumActionType.GET_UNFRIENDS_LIST, accessToken);
        JSONObject retobj = wumConnectWithWs.get_retobj();
        if (retobj != null) {
            String list_uf = "";
            try {
                //Log.i(TAG, retobj.toString());
                JSONArray uf;
                uf = retobj.getJSONArray("unfriends_list");
                //JSONObject ufarray = (JSONObject) retobj.getJSONObject("unfriends_list");
                //getJSONArray("unfriends_list");
                for (int i = 0; i < uf.length(); i++) {
//                Log.i(TAG, uf.getString(i));
                    if (list_uf == "") {
                        list_uf = uf.getString(i);
                    } else {
                        list_uf = list_uf + ", " + uf.getString(i);
                    }
                }
//            SendEmail.send(list_uf);
                //SendEmail.send(retobj.toString());
            } catch (JSONException je) {
                Log.e(TAG, je.getMessage());
            }
            Log.i(TAG, "Unfriended: " + list_uf);
            return (list_uf);
        } else {
            if (AccessToken.getCurrentAccessToken() == null)
            {
                Log.i(TAG, "Please login to facebook");
            }
            return "";
        }
    }
    public static void sendNotification(Context context, String notification) {

        //Get an instance of NotificationManager//

        NotificationCompat.Builder mBuilder = //(android.support.v7.app.NotificationCompat.Builder)
                new NotificationCompat.Builder(context)
                        .setSmallIcon(R.drawable.ic_stat_name)
                        .setContentTitle("WUM")
                        .setContentText("The following friends has unfriended you: " + notification);

        // Gets an instance of the NotificationManager service//

        NotificationManager mNotificationManager =

                (NotificationManager)context.getSystemService(Context.NOTIFICATION_SERVICE);

        // When you issue multiple notifications about the same type of event,
        // it’s best practice for your app to try to update an existing notification
        // with this new information, rather than immediately creating a new notification.
        // If you want to update this notification at a later date, you need to assign it an ID.
        // You can then use this ID whenever you issue a subsequent notification.
        // If the previous notification is still visible, the system will update this existing notification,
        // rather than create a new one. In this example, the notification’s ID is 001//

        //NotificationManager.notify().

        mNotificationManager.notify(001, mBuilder.build());
    }

    public static String getDate()
    {
        DateFormat df = new SimpleDateFormat("MM/dd/yyyy HH:mm:ss");
        Date today = Calendar.getInstance().getTime();
        String reportDate = df.format(today);
        return (reportDate);
    }
}
