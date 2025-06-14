import { Injectable } from "@angular/core";
import { AuthController } from "../controllers/auth.controller";

@Injectable({ providedIn: "root" })
export class SettingsService {
  constructor(private readonly authController: AuthController) {}

  public showFileHashes() {
    return this.authController.getUser()?.settings?.storageSettings?.showFileHash ?? false;
  }
}
