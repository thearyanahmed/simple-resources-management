import moment from "moment";
export function displayDate(date : Date) : string {
    // 8:04pm 4th sept, 21
    return moment(date).format('h:mm a DD MMM, YY')
}