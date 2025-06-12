export function copyTextToClipboard(text?: string) {
  if (!text) {
    return;
  }

  return navigator.clipboard.writeText(text);
}
