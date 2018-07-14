package il.co.itzikm.repeatingalarm;

public enum WumActionType {
    SUBSCRIBE ("https://www.itzikm.co.il/ws_subscribe.php"),
    UNSUBSCRIBE ("https://www.itzikm.co.il/ws_unsubscribe.php"),
    ISSUB ("https://www.itzikm.co.il/ws_issub.php"),
    UPDATE ("https://www.itzikm.co.il/ws_update.php"),
    UPDATE_USER ("https://www.itzikm.co.il/ws_updateuser.php"),
    GET_UNFRIENDS_LIST ("https://www.itzikm.co.il/ws_get_unfriends.php");

    WumActionType(String url) {
        this.url = url;
    }
    private final String url;
    public String url() { return url; }
}
