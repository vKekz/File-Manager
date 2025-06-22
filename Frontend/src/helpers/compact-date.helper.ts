export function calculateCompactDate(dateString: string) {
  const date = new Date(dateString);
  const dateOptions = {
    day: "2-digit",
    month: "2-digit",
    year: "2-digit",
    timeZone: "Europe/Berlin",
  } as Intl.DateTimeFormatOptions;
  const timeOptions = {
    hour12: false,
    hour: "2-digit",
    minute: "2-digit",
    timeZone: "Europe/Berlin",
  } as Intl.DateTimeFormatOptions;

  return `${date.toLocaleDateString("de-DE", dateOptions)} ${date.toLocaleTimeString("de-DE", timeOptions)}`;
}
