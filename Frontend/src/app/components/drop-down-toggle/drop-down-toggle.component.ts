import { Component, Input } from "@angular/core";
import { DropDownType } from "../../../enums/drop-down-type.enum";
import { DropDownMenuComponent } from "../drop-down-menu/drop-down-menu.component";

@Component({
  selector: "app-drop-down-toggle",
  imports: [DropDownMenuComponent],
  templateUrl: "./drop-down-toggle.component.html",
  styleUrl: "./drop-down-toggle.component.css",
})
export class DropDownToggleComponent {
  @Input({ required: true })
  public type: DropDownType = DropDownType.File;

  protected isToggled: boolean = false;

  protected toggleMenu() {
    this.isToggled = !this.isToggled;
  }
}
