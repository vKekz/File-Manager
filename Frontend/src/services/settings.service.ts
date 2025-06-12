import { Injectable } from "@angular/core";

@Injectable({ providedIn: "root" })
export class SettingsService {
  public showFileHashes() {
    return false;
  }
}
